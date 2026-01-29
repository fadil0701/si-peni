<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RegisterAset;
use App\Models\MasterPegawai;
use App\Models\MasterUnitKerja;
use App\Models\MasterGudang;
use App\Models\DataInventory;
use Illuminate\Support\Facades\Auth;

class RegisterAsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Filter untuk user pegawai - hanya gudang unit mereka sendiri
        // Admin gudang dan pengurus barang bisa melihat semua gudang unit
        $pegawai = null;
        $userUnitKerjaId = null;
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasAnyRole(['admin', 'admin_gudang', 'pengurus_barang'])) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                $userUnitKerjaId = $pegawai->id_unit_kerja;
            }
        }
        
        // Ambil semua gudang unit yang memiliki RegisterAset atau InventoryItem dengan jenis ASET
        // Ambil unit kerja yang punya RegisterAset
        $unitKerjaIdsWithRegisterAset = RegisterAset::whereHas('inventory', function($q) {
                $q->where('jenis_inventory', 'ASET');
            })
            ->pluck('id_unit_kerja')
            ->unique()
            ->filter()
            ->toArray();
        
        // Ambil gudang unit yang:
        // 1. Punya InventoryItem dengan jenis ASET, ATAU
        // 2. Unit kerjanya punya RegisterAset
        $gudangUnits = MasterGudang::where('jenis_gudang', 'UNIT')
            ->where(function($q) use ($unitKerjaIdsWithRegisterAset) {
                // Gudang yang punya InventoryItem dengan jenis ASET
                $q->whereHas('inventoryItems', function($q2) {
                    $q2->whereHas('inventory', function($q3) {
                        $q3->where('jenis_inventory', 'ASET');
                    });
                });
                
                // ATAU gudang yang unit kerjanya punya RegisterAset
                if (!empty($unitKerjaIdsWithRegisterAset)) {
                    $q->orWhereIn('id_unit_kerja', $unitKerjaIdsWithRegisterAset);
                }
            })
            ->when($userUnitKerjaId, function($q) use ($userUnitKerjaId) {
                $q->where('id_unit_kerja', $userUnitKerjaId);
            })
            ->with('unitKerja')
            ->get();
        
        // Hitung KIB dan KIR untuk setiap gudang unit
        foreach ($gudangUnits as $gudang) {
            // KIR: RegisterAset yang memiliki ruangan di unit kerja ini
            $kirCount = RegisterAset::where('id_unit_kerja', $gudang->id_unit_kerja)
                ->whereNotNull('id_ruangan')
                ->count();
            
            // KIB: RegisterAset yang id_unit_kerja = unit kerja gudang ini tapi belum punya ruangan (masih di gudang unit)
            $kibCount = RegisterAset::where('id_unit_kerja', $gudang->id_unit_kerja)
                ->whereNull('id_ruangan')
                ->count();
            
            $gudang->kir_count = $kirCount;
            $gudang->kib_count = $kibCount;
            $gudang->total_aset = $kirCount + $kibCount;
        }
        
        // Ambil data gudang pusat (KIB saja) - HANYA untuk admin, admin_gudang, pengurus_barang, bukan untuk pegawai
        $gudangPusatData = null;
        if (!$userUnitKerjaId || $user->hasAnyRole(['admin', 'admin_gudang', 'pengurus_barang'])) {
            $gudangPusat = MasterGudang::where('jenis_gudang', 'PUSAT')
                ->where('kategori_gudang', 'ASET')
                ->first();
            
            if ($gudangPusat) {
                // KIB untuk gudang pusat:
                // 1. InventoryItem yang masih di gudang pusat dan BELUM punya RegisterAset (belum didistribusikan)
                // 2. RegisterAset yang masih di gudang pusat (id_unit_kerja = null atau belum didistribusikan)
                
                // Hitung InventoryItem yang masih di gudang pusat dan belum punya RegisterAset
                $inventoryItemsKib = \App\Models\InventoryItem::whereHas('inventory', function($q) use ($gudangPusat) {
                        $q->where('id_gudang', $gudangPusat->id_gudang)
                          ->where('jenis_inventory', 'ASET');
                    })
                    ->where('id_gudang', $gudangPusat->id_gudang)
                    ->whereDoesntHave('registerAset')
                    ->count();
                
                // Hitung RegisterAset yang masih di gudang pusat (belum didistribusikan ke unit kerja)
                $registerAsetKib = RegisterAset::whereHas('inventory', function($q) use ($gudangPusat) {
                        $q->where('id_gudang', $gudangPusat->id_gudang)
                          ->where('jenis_inventory', 'ASET');
                    })
                    ->whereNull('id_unit_kerja')
                    ->whereNull('id_ruangan')
                    ->count();
                
                $kibCount = $inventoryItemsKib + $registerAsetKib;
                
                // KIR untuk gudang pusat biasanya 0 (karena KIR adalah aset di ruangan unit)
                $kirCount = 0;
                
                $gudangPusatData = [
                    'id' => 'pusat',
                    'nama' => $gudangPusat->nama_gudang,
                    'total_aset' => $kibCount,
                    'kib_count' => $kibCount,
                    'kir_count' => $kirCount,
                ];
            }
        }
        
        return view('asset.register-aset.index', compact('gudangUnits', 'gudangPusatData'));
    }

    /**
     * Display register aset by unit kerja.
     */
    public function showUnitKerja(Request $request, $unitKerjaId)
    {
        $user = Auth::user();
        
        // Filter KIB/KIR
        $filter = $request->get('filter', 'semua');
        
        // Inisialisasi variabel
        $isPusat = false;
        $gudangUnit = null;
        
        // Jika 'pusat', ambil data gudang pusat
        if ($unitKerjaId == 'pusat') {
            $gudangPusat = MasterGudang::where('jenis_gudang', 'PUSAT')
                ->where('kategori_gudang', 'ASET')
                ->firstOrFail();
            
            // Untuk gudang pusat, ambil data dari InventoryItem (hasil auto add row)
            // KIB: InventoryItem yang id_gudang = gudang pusat atau belum didistribusikan
            // Hanya ambil yang belum di-register ke unit kerja
            // Logika: Ambil semua InventoryItem yang masih di gudang pusat
            // Lalu filter berdasarkan jumlah RegisterAset per id_inventory
            $allInventoryItems = \App\Models\InventoryItem::with([
                'inventory.dataBarang',
                'inventory.gudang.unitKerja',
                'gudang.unitKerja',
                'ruangan.unitKerja'
            ])->whereHas('inventory', function($q) use ($gudangPusat) {
                $q->where('jenis_inventory', 'ASET');
            });
            
            // Filter KIB/KIR untuk gudang pusat
            if ($filter == 'kir') {
                // Gudang pusat tidak punya KIR, jadi kosongkan query
                $allInventoryItems->whereRaw('1 = 0');
            } elseif ($filter == 'kib') {
                // KIB: InventoryItem yang masih di gudang pusat (belum didistribusikan ke unit)
                $allInventoryItems->where(function($q) use ($gudangPusat) {
                    $q->where('id_gudang', $gudangPusat->id_gudang)
                      ->orWhereNull('id_gudang'); // Belum didistribusikan
                });
            } else {
                // Filter 'semua': semua InventoryItem yang terkait dengan gudang pusat
                $allInventoryItems->where(function($q) use ($gudangPusat) {
                    $q->where('id_gudang', $gudangPusat->id_gudang)
                      ->orWhereNull('id_gudang');
                });
            }
            
            // Ambil semua InventoryItem yang memenuhi kriteria di atas
            $inventoryItemsList = $allInventoryItems->orderBy('kode_register')->get();
            
            // Hitung jumlah RegisterAset per id_inventory yang sudah di-register ke unit kerja
            $inventoryIds = $inventoryItemsList->pluck('id_inventory')->unique()->toArray();
            $registerAsetCounts = RegisterAset::whereIn('id_inventory', $inventoryIds)
                ->whereNotNull('id_unit_kerja')
                ->selectRaw('id_inventory, COUNT(*) as count')
                ->groupBy('id_inventory')
                ->pluck('count', 'id_inventory');
            
            // Hitung jumlah InventoryItem per id_inventory
            $inventoryItemCounts = $inventoryItemsList->groupBy('id_inventory')->map(function($items) {
                return $items->count();
            });
            
            // Filter: hanya ambil InventoryItem yang jumlahnya masih lebih besar dari jumlah RegisterAset
            $filteredItems = $inventoryItemsList->filter(function($item) use ($inventoryItemCounts, $registerAsetCounts) {
                $inventoryId = $item->id_inventory;
                $itemCount = $inventoryItemCounts[$inventoryId] ?? 0;
                $registerCount = $registerAsetCounts[$inventoryId] ?? 0;
                
                // Jika jumlah RegisterAset sudah sama atau lebih dari jumlah InventoryItem, sembunyikan
                return $registerCount < $itemCount;
            })->pluck('id_item')->toArray();
            
            // Buat query dari filtered items
            if (!empty($filteredItems)) {
                $query = \App\Models\InventoryItem::with([
                    'inventory.dataBarang',
                    'inventory.gudang.unitKerja',
                    'gudang.unitKerja',
                    'ruangan.unitKerja'
                ])->whereIn('id_item', $filteredItems);
            } else {
                $query = \App\Models\InventoryItem::whereRaw('1 = 0');
            }
            
            // Urutkan berdasarkan kode_register
            $query->orderBy('kode_register');
            
            $title = $gudangPusat->nama_gudang;
            $isPusat = true;
        } else {
            // Ambil data gudang unit
            $gudangUnit = MasterGudang::where('id_gudang', $unitKerjaId)
                ->where('jenis_gudang', 'UNIT')
                ->with('unitKerja')
                ->firstOrFail();
            
            // Untuk gudang unit, ambil data dari RegisterAset yang id_unit_kerja = unit kerja gudang ini
            // Lalu ambil InventoryItem yang sesuai dengan RegisterAset tersebut
            // Pendekatan: Ambil RegisterAset dulu dengan filter KIB/KIR
            $registerAsetQuery = RegisterAset::where('id_unit_kerja', $gudangUnit->id_unit_kerja);
            
            // Filter KIB/KIR untuk RegisterAset
            if ($filter == 'kir') {
                $registerAsetQuery->whereNotNull('id_ruangan');
            } elseif ($filter == 'kib') {
                $registerAsetQuery->whereNull('id_ruangan');
            }
            // else: filter 'semua' - tidak perlu filter tambahan
            
            $registerAsets = $registerAsetQuery->get();
            
            if ($registerAsets->isEmpty()) {
                // Jika tidak ada RegisterAset, return empty collection
                $query = \App\Models\InventoryItem::whereRaw('1 = 0');
            } else {
                // Ambil InventoryItem berdasarkan mapping RegisterAset
                // Untuk setiap RegisterAset, ambil InventoryItem yang sesuai
                // Mapping: RegisterAset.id_inventory -> InventoryItem.id_inventory
                // Tapi hanya ambil InventoryItem yang belum digunakan oleh RegisterAset lain di unit kerja ini
                $registerAsetInventoryIds = $registerAsets->pluck('id_inventory')->unique()->toArray();
                
                // Ambil semua InventoryItem untuk id_inventory tersebut
                $allInventoryItems = \App\Models\InventoryItem::whereIn('id_inventory', $registerAsetInventoryIds)
                    ->whereHas('inventory', function($q) {
                        $q->where('jenis_inventory', 'ASET');
                    })
                    ->orderBy('id_item')
                    ->get();
                
                // Mapping: untuk setiap RegisterAset, ambil InventoryItem yang sesuai
                // Strategi: ambil InventoryItem pertama yang belum digunakan untuk id_inventory tersebut
                $usedItemIds = [];
                $selectedItemIds = [];
                
                foreach ($registerAsets as $registerAset) {
                    // Cari InventoryItem untuk id_inventory ini yang belum digunakan
                    $availableItems = $allInventoryItems->where('id_inventory', $registerAset->id_inventory)
                        ->whereNotIn('id_item', $usedItemIds)
                        ->first();
                    
                    if ($availableItems) {
                        $selectedItemIds[] = $availableItems->id_item;
                        $usedItemIds[] = $availableItems->id_item;
                    }
                }
                
                if (!empty($selectedItemIds)) {
                    // Ambil InventoryItem yang sudah dipilih dengan eager loading
                    $query = \App\Models\InventoryItem::with([
                        'inventory.dataBarang',
                        'inventory.gudang.unitKerja',
                        'inventory.registerAset' => function($q) use ($gudangUnit) {
                            $q->where('id_unit_kerja', $gudangUnit->id_unit_kerja);
                        },
                        'gudang.unitKerja',
                        'ruangan.unitKerja'
                    ])->whereIn('id_item', $selectedItemIds);
                } else {
                    $query = \App\Models\InventoryItem::whereRaw('1 = 0');
                }
            }
            
            // Urutkan berdasarkan kode_register
            $query->orderBy('kode_register');
            
            $title = $gudangUnit->nama_gudang . ($gudangUnit->unitKerja ? ' (' . $gudangUnit->unitKerja->nama_unit_kerja . ')' : '');
            $isPusat = false;
            
            // Filter berdasarkan gudang unit untuk kepala_unit dan pegawai
            if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasAnyRole(['admin', 'admin_gudang', 'pengurus_barang'])) {
                $pegawai = MasterPegawai::where('user_id', $user->id)->first();
                if ($pegawai && $gudangUnit->id_unit_kerja && $pegawai->id_unit_kerja != $gudangUnit->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat melihat aset dari gudang unit Anda sendiri');
                }
            }
        }
        
        // Filter berdasarkan gudang (jika ada request)
        if ($request->filled('id_gudang')) {
            $query->where('id_gudang', $request->id_gudang);
        }
        
        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $inventoryItems = $query->paginate($perPage)->appends($request->query());
        
        // Preload RegisterAset untuk setiap InventoryItem untuk menghindari N+1 query
        $inventoryItemIds = $inventoryItems->pluck('id_item')->toArray();
        $registerAsetsMap = []; // Map berdasarkan id_inventory -> array of RegisterAset
        $registerAsetItemMap = []; // Map berdasarkan id_item -> RegisterAset (untuk mapping yang lebih tepat)
        
        if (!empty($inventoryItemIds)) {
            // Untuk gudang pusat, ambil semua RegisterAset (tidak filter berdasarkan unit kerja)
            // Untuk gudang unit, ambil RegisterAset berdasarkan unit kerja
            if ($isPusat) {
                $registerAsets = RegisterAset::whereIn('id_inventory', $inventoryItems->pluck('id_inventory')->unique())
                    ->with([
                        'ruangan.unitKerja', 
                        'kartuInventarisRuangan.penanggungJawab',
                        'unitKerja.gudang' => function($q) {
                            $q->where('jenis_gudang', 'UNIT')->where('kategori_gudang', 'ASET');
                        }
                    ])
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy('id_inventory');
            } else {
                $unitKerjaIdForQuery = isset($gudangUnit) && $gudangUnit->unitKerja ? $gudangUnit->unitKerja->id_unit_kerja : null;
                $registerAsets = RegisterAset::where('id_unit_kerja', $unitKerjaIdForQuery)
                    ->whereIn('id_inventory', $inventoryItems->pluck('id_inventory')->unique())
                    ->with([
                        'ruangan.unitKerja', 
                        'kartuInventarisRuangan.penanggungJawab',
                        'unitKerja.gudang' => function($q) {
                            $q->where('jenis_gudang', 'UNIT')->where('kategori_gudang', 'ASET');
                        }
                    ])
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy('id_inventory');
            }
            
            // Map RegisterAset berdasarkan id_inventory untuk akses cepat di view
            foreach ($registerAsets as $inventoryId => $registers) {
                $registerAsetsMap[$inventoryId] = $registers;
            }
            
            // Buat mapping yang lebih tepat: InventoryItem -> RegisterAset
            // Karena format nomor_register sudah terpisah dari kode_register, kita mapping berdasarkan urutan
            // InventoryItem dan RegisterAset dengan id_inventory yang sama akan dipasangkan berdasarkan urutan
            foreach ($inventoryItems as $item) {
                if (isset($registerAsetsMap[$item->id_inventory])) {
                    // Ambil semua InventoryItem dengan id_inventory yang sama, urutkan berdasarkan id_item
                    $itemsForInventory = $inventoryItems->where('id_inventory', $item->id_inventory)
                        ->sortBy('id_item')
                        ->values();
                    
                    // Ambil semua RegisterAset dengan id_inventory yang sama, urutkan berdasarkan created_at
                    $registersForInventory = $registerAsetsMap[$item->id_inventory]
                        ->sortBy('created_at')
                        ->values();
                    
                    // Cari index dari item ini dalam daftar items untuk inventory yang sama
                    $itemIndex = $itemsForInventory->search(function($invItem) use ($item) {
                        return $invItem->id_item === $item->id_item;
                    });
                    
                    // Jika ditemukan dan ada RegisterAset di index yang sama, pasangkan
                    if ($itemIndex !== false && isset($registersForInventory[$itemIndex])) {
                        $registerAsetItemMap[$item->id_item] = $registersForInventory[$itemIndex];
                    }
                }
            }
        }
        
        // Ambil data untuk dropdown filter
        $gudangs = MasterGudang::where('kategori_gudang', 'ASET')
            ->with('unitKerja')
            ->orderBy('nama_gudang')
            ->get();
        
        // Pass gudangUnit ke view untuk digunakan di view
        $gudangUnitForView = isset($gudangUnit) ? $gudangUnit : null;
        
        return view('asset.register-aset.unit-kerja.show', compact('inventoryItems', 'title', 'filter', 'unitKerjaId', 'gudangs', 'isPusat', 'gudangUnitForView', 'registerAsetsMap', 'registerAsetItemMap'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * NOTE: RegisterAset sekarang dibuat otomatis saat penerimaan barang dikonfirmasi.
     * Form ini hanya untuk kasus khusus (misalnya aset yang sudah ada di gudang unit tanpa melalui distribusi).
     */
    public function create()
    {
        // Hanya admin dan admin_gudang yang bisa create
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        // Query: Ambil InventoryItem yang belum punya RegisterAset
        // RegisterAset dibuat otomatis saat penerimaan, jadi ini untuk kasus khusus saja
        // Filter: InventoryItem yang id_inventory-nya belum penuh dibuat RegisterAset
        // Logika: Untuk setiap id_inventory, hitung jumlah InventoryItem dan jumlah RegisterAset
        // Jika jumlah RegisterAset < jumlah InventoryItem, tampilkan InventoryItem yang tersisa
        
        // Ambil semua InventoryItem ASET yang aktif
        $allInventoryItems = \App\Models\InventoryItem::with('inventory.dataBarang', 'inventory.gudang', 'gudang')
            ->whereHas('inventory', function($q) {
                $q->where('jenis_inventory', 'ASET')
                  ->where('status_inventory', 'AKTIF');
            })
            ->orderBy('kode_register')
            ->get();
        
        // Kelompokkan berdasarkan id_inventory dan hitung jumlah RegisterAset per id_inventory
        $inventoryItemCounts = $allInventoryItems->groupBy('id_inventory')->map(function($items) {
            return $items->count();
        });
        
        $registerAsetCounts = RegisterAset::whereIn('id_inventory', $inventoryItemCounts->keys())
            ->selectRaw('id_inventory, COUNT(*) as count')
            ->groupBy('id_inventory')
            ->pluck('count', 'id_inventory');
        
        // Filter: hanya ambil InventoryItem yang id_inventory-nya masih punya slot untuk RegisterAset
        $inventoryItems = $allInventoryItems->filter(function($item) use ($inventoryItemCounts, $registerAsetCounts) {
            $inventoryId = $item->id_inventory;
            $itemCount = $inventoryItemCounts[$inventoryId] ?? 0;
            $registerCount = $registerAsetCounts[$inventoryId] ?? 0;
            
            // Jika jumlah RegisterAset sudah sama atau lebih dari jumlah InventoryItem, sembunyikan
            return $registerCount < $itemCount;
        })->values();
        
        // Ambil semua unit kerja
        $unitKerjas = MasterUnitKerja::orderBy('nama_unit_kerja')->get();
        
        // Ambil semua ruangan (akan difilter berdasarkan unit kerja di frontend)
        $ruangans = \App\Models\MasterRuangan::with('unitKerja')
            ->orderBy('nama_ruangan')
            ->get();
        
        return view('asset.register-aset.create', compact('inventoryItems', 'unitKerjas', 'ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Hanya admin dan admin_gudang yang bisa store
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        // Validasi input
        $validated = $request->validate([
            'id_inventory' => 'required|exists:data_inventory,id_inventory',
            'id_unit_kerja' => 'required|exists:master_unit_kerja,id_unit_kerja',
            'id_ruangan' => 'nullable|exists:master_ruangan,id_ruangan',
            'nomor_register' => 'nullable|string|max:100', // Bisa kosong, akan di-generate otomatis
            'kondisi_aset' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT',
            'status_aset' => 'required|in:AKTIF,NONAKTIF',
            'tanggal_perolehan' => 'required|date',
        ]);
        
        // Cek apakah inventory adalah jenis ASET
        $inventory = DataInventory::findOrFail($validated['id_inventory']);
        if ($inventory->jenis_inventory !== 'ASET') {
            return back()->withErrors(['id_inventory' => 'Inventory yang dipilih harus berjenis ASET'])->withInput();
        }
        
        // Generate nomor register otomatis jika tidak diisi
        if (empty($validated['nomor_register'])) {
            $validated['nomor_register'] = $this->generateNomorRegister(
                $validated['id_unit_kerja'],
                $validated['id_ruangan'] ?? null,
                $validated['tanggal_perolehan']
            );
        } else {
            // Jika nomor register sudah ada, buat yang unik dengan menambahkan suffix
            $nomorRegister = $validated['nomor_register'];
            $counter = 1;
            while (RegisterAset::where('nomor_register', $nomorRegister)->exists()) {
                $nomorRegister = $validated['nomor_register'] . '-' . $counter;
                $counter++;
            }
            $validated['nomor_register'] = $nomorRegister;
        }
        
        // Buat register aset
        $registerAset = RegisterAset::create($validated);
        
        // Jika RegisterAset dibuat dengan ruangan, buat KartuInventarisRuangan otomatis
        if (!empty($validated['id_ruangan'])) {
            // Cek apakah sudah ada KIR untuk RegisterAset dan ruangan ini
            $existingKir = \App\Models\KartuInventarisRuangan::where('id_register_aset', $registerAset->id_register_aset)
                ->where('id_ruangan', $validated['id_ruangan'])
                ->first();
            
            if (!$existingKir) {
                // Buat KIR baru
                \App\Models\KartuInventarisRuangan::create([
                    'id_register_aset' => $registerAset->id_register_aset,
                    'id_ruangan' => $validated['id_ruangan'],
                    'id_penanggung_jawab' => null, // Bisa diisi nanti
                    'tanggal_penempatan' => $validated['tanggal_perolehan'] ?? now(),
                ]);
            }
            
            // Update InventoryItem untuk set id_ruangan
            // Ambil InventoryItem pertama dari inventory ini yang belum punya ruangan
            $inventoryItem = \App\Models\InventoryItem::where('id_inventory', $validated['id_inventory'])
                ->whereNull('id_ruangan')
                ->first();
            
            // Jika tidak ada yang belum punya ruangan, ambil yang pertama saja
            if (!$inventoryItem) {
                $inventoryItem = \App\Models\InventoryItem::where('id_inventory', $validated['id_inventory'])
                    ->first();
            }
            
            if ($inventoryItem) {
                $inventoryItem->update(['id_ruangan' => $validated['id_ruangan']]);
            }
        }
        
        return redirect()->route('asset.register-aset.show', $registerAset->id_register_aset)
            ->with('success', 'Register aset berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $registerAset = RegisterAset::with([
            'inventory.dataBarang',
            'inventory.gudang',
            'inventory.satuan',
            'inventory.sumberAnggaran',
            'unitKerja',
            'kartuInventarisRuangan',
            'mutasiAset',
            'permintaanPemeliharaan',
            'jadwalMaintenance'
        ])->findOrFail($id);
        $user = Auth::user();

        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($registerAset->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat melihat aset dari gudang unit Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki gudang unit');
            }
        }

        return view('asset.register-aset.show', compact('registerAset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $registerAset = RegisterAset::with([
            'inventory.dataBarang', 
            'unitKerja',
            'ruangan',
            'kartuInventarisRuangan.penanggungJawab'
        ])->findOrFail($id);
        $user = Auth::user();

        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($registerAset->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat mengedit aset dari gudang unit Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki gudang unit');
            }
        }

        // Ambil ruangan untuk dropdown (filter berdasarkan unit kerja)
        $ruangans = \App\Models\MasterRuangan::where('id_unit_kerja', $registerAset->id_unit_kerja)
            ->orderBy('nama_ruangan')
            ->get();

        // Ambil pegawai untuk dropdown penanggung jawab (filter berdasarkan unit kerja)
        $pegawais = MasterPegawai::where('id_unit_kerja', $registerAset->id_unit_kerja)
            ->orderBy('nama_pegawai')
            ->get();

        // Ambil KIR jika ada
        $kir = $registerAset->kartuInventarisRuangan->first();

        return view('asset.register-aset.edit', compact('registerAset', 'ruangans', 'pegawais', 'kir'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $registerAset = RegisterAset::findOrFail($id);
        $user = Auth::user();

        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($registerAset->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat mengupdate aset dari gudang unit Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki gudang unit');
            }
        }

        // Validasi input
        $validated = $request->validate([
            'id_ruangan' => 'nullable|exists:master_ruangan,id_ruangan',
            'kondisi_aset' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT',
            'status_aset' => 'required|in:AKTIF,NONAKTIF',
            'tanggal_perolehan' => 'required|date',
            'id_penanggung_jawab' => 'nullable|exists:master_pegawai,id',
            'regenerate_nomor_register' => 'nullable|boolean', // Flag untuk regenerate nomor register
        ]);

        DB::beginTransaction();
        try {
            // Jika unit kerja atau ruangan berubah, regenerate nomor register
            $shouldRegenerate = $request->has('regenerate_nomor_register') && $request->regenerate_nomor_register;
            $unitKerjaChanged = $registerAset->id_unit_kerja != $registerAset->id_unit_kerja; // Tidak berubah karena tidak bisa diubah
            $ruanganChanged = $registerAset->id_ruangan != $validated['id_ruangan'];
            
            if ($shouldRegenerate || $ruanganChanged) {
                // Generate nomor register baru dengan format baru
                $newNomorRegister = $this->generateNomorRegister(
                    $registerAset->id_unit_kerja,
                    $validated['id_ruangan'] ?? null,
                    $validated['tanggal_perolehan']
                );
                
                // Update nomor register
                $registerAset->update([
                    'id_ruangan' => $validated['id_ruangan'],
                    'kondisi_aset' => $validated['kondisi_aset'],
                    'status_aset' => $validated['status_aset'],
                    'tanggal_perolehan' => $validated['tanggal_perolehan'],
                    'nomor_register' => $newNomorRegister,
                ]);
            } else {
                // Update register aset tanpa mengubah nomor register
                $registerAset->update([
                    'id_ruangan' => $validated['id_ruangan'],
                    'kondisi_aset' => $validated['kondisi_aset'],
                    'status_aset' => $validated['status_aset'],
                    'tanggal_perolehan' => $validated['tanggal_perolehan'],
                ]);
            }

            // Jika ada ruangan, update InventoryItem dan buat/update KIR
            if ($validated['id_ruangan']) {
                // Update InventoryItem yang sesuai dengan RegisterAset ini
                // Ambil InventoryItem pertama yang belum punya ruangan untuk id_inventory ini
                $inventoryItem = \App\Models\InventoryItem::where('id_inventory', $registerAset->id_inventory)
                    ->where(function($q) {
                        $q->whereNull('id_ruangan')
                          ->orWhere('id_ruangan', '');
                    })
                    ->first();

                if ($inventoryItem) {
                    $inventoryItem->update(['id_ruangan' => $validated['id_ruangan']]);
                }

                // Buat atau Update KIR
                $kir = \App\Models\KartuInventarisRuangan::firstOrCreate([
                    'id_register_aset' => $registerAset->id_register_aset,
                    'id_ruangan' => $validated['id_ruangan'],
                ], [
                    'id_penanggung_jawab' => $validated['id_penanggung_jawab'] ?? null,
                    'tanggal_penempatan' => $validated['tanggal_perolehan'] ?? now(),
                ]);

                // Update penanggung jawab jika diubah
                if (!$kir->wasRecentlyCreated && isset($validated['id_penanggung_jawab'])) {
                    $kir->update(['id_penanggung_jawab' => $validated['id_penanggung_jawab']]);
                }
            } else {
                // Jika ruangan dihapus, hapus KIR dan update InventoryItem
                \App\Models\KartuInventarisRuangan::where('id_register_aset', $registerAset->id_register_aset)
                    ->delete();

                // Update InventoryItem untuk set id_ruangan menjadi null
                // Ambil InventoryItem yang memiliki ruangan yang sama dengan RegisterAset ini
                $kir = \App\Models\KartuInventarisRuangan::where('id_register_aset', $registerAset->id_register_aset)
                    ->first();
                
                if ($kir) {
                    $inventoryItems = \App\Models\InventoryItem::where('id_inventory', $registerAset->id_inventory)
                        ->where('id_ruangan', $kir->id_ruangan)
                        ->get();
                    
                    foreach ($inventoryItems as $inventoryItem) {
                        $inventoryItem->update(['id_ruangan' => null]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('asset.register-aset.show', $registerAset->id_register_aset)
                ->with('success', 'Register aset berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating register aset: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Hanya admin dan admin_gudang yang bisa delete
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        $registerAset = RegisterAset::findOrFail($id);
        $registerAset->delete();

        return redirect()->route('asset.register-aset.index')
            ->with('success', 'Register aset berhasil dihapus.');
    }

    /**
     * Generate nomor register otomatis
     * Format: [ID_UNIT_KERJA]/[ID_RUANGAN]/[URUT]
     * Jika tidak ada ruangan: [ID_UNIT_KERJA]/[URUT]
     */
    protected function generateNomorRegister($idUnitKerja, $idRuangan = null, $tanggalPerolehan = null)
    {
        $tahun = $tanggalPerolehan ? date('Y', strtotime($tanggalPerolehan)) : date('Y');
        
        // Format baru: ID_UNIT_KERJA/ID_RUANGAN/URUT atau ID_UNIT_KERJA/URUT
        if ($idRuangan) {
            $prefix = sprintf('%03d/%03d', $idUnitKerja, $idRuangan);
        } else {
            $prefix = sprintf('%03d', $idUnitKerja);
        }
        
        // Cari nomor urut terakhir untuk kombinasi unit kerja + ruangan + tahun ini
        $lastRegister = RegisterAset::where('id_unit_kerja', $idUnitKerja)
            ->where(function($q) use ($idRuangan) {
                if ($idRuangan) {
                    $q->where('id_ruangan', $idRuangan);
                } else {
                    $q->whereNull('id_ruangan');
                }
            })
            ->whereYear('tanggal_perolehan', $tahun)
            ->where('nomor_register', 'like', $prefix . '/%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(nomor_register, "/", -1) AS UNSIGNED) DESC')
            ->first();
        
        $urut = 1;
        if ($lastRegister) {
            // Extract nomor urut dari nomor register terakhir
            $parts = explode('/', $lastRegister->nomor_register);
            $lastUrut = (int)end($parts);
            $urut = $lastUrut + 1;
        }
        
        return sprintf('%s/%04d', $prefix, $urut);
    }
}

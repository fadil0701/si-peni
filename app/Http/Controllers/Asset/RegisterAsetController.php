<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        
        // Ambil gudang unit yang punya InventoryItem dengan jenis ASET
        // Setelah distribusi, InventoryItem.id_gudang diupdate ke gudang tujuan
        $gudangUnits = MasterGudang::whereHas('inventoryItems', function($q) {
                $q->whereHas('inventory', function($q2) {
                    $q2->where('jenis_inventory', 'ASET');
                });
            })
            ->where('jenis_gudang', 'UNIT')
            ->when($userUnitKerjaId, function($q) use ($userUnitKerjaId) {
                $q->where('id_unit_kerja', $userUnitKerjaId);
            })
            ->with('unitKerja')
            ->get();
        
        // Hitung KIR untuk setiap gudang unit (tidak perlu KIB untuk gudang unit)
        foreach ($gudangUnits as $gudang) {
            // KIR: aset yang sudah didistribusikan ke gudang unit ini (dari InventoryItem yang id_gudang = gudang ini)
            $gudang->kir_count = \App\Models\InventoryItem::where('id_gudang', $gudang->id_gudang)
                ->whereHas('inventory', function($q) {
                    $q->where('jenis_inventory', 'ASET');
                })
                ->count();
            
            // Total aset untuk gudang unit = KIR saja
            $gudang->total_aset = $gudang->kir_count;
        }
        
        // Ambil data gudang pusat (KIB saja) - HANYA untuk admin, admin_gudang, pengurus_barang, bukan untuk pegawai
        $gudangPusatData = null;
        if (!$userUnitKerjaId || $user->hasAnyRole(['admin', 'admin_gudang', 'pengurus_barang'])) {
            $gudangPusat = MasterGudang::where('jenis_gudang', 'PUSAT')
                ->where('kategori_gudang', 'ASET')
                ->first();
            
            if ($gudangPusat) {
                // KIB: total aset di gudang pusat berdasarkan DataInventory (tidak berkurang meski sudah didistribusikan)
                $kibCount = \App\Models\DataInventory::where('id_gudang', $gudangPusat->id_gudang)
                    ->where('jenis_inventory', 'ASET')
                    ->sum('qty_input');
                
                $gudangPusatData = [
                    'id' => 'pusat',
                    'nama' => $gudangPusat->nama_gudang,
                    'total_aset' => $kibCount,
                    'kib_count' => $kibCount,
                    'kir_count' => 0,
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
        
        // Jika 'pusat', ambil data gudang pusat
        if ($unitKerjaId == 'pusat') {
            $gudangPusat = MasterGudang::where('jenis_gudang', 'PUSAT')
                ->where('kategori_gudang', 'ASET')
                ->firstOrFail();
            
            // Untuk gudang pusat, hanya tampilkan KIB (tidak ada KIR)
            $query = RegisterAset::with([
                'inventory.dataBarang',
                'inventory.gudang',
                'inventory.gudang.unitKerja',
                'unitKerja'
            ])->whereHas('inventory', function($q) use ($gudangPusat) {
                $q->where('id_gudang', $gudangPusat->id_gudang)
                  ->where('jenis_inventory', 'ASET');
            });
            
            // Filter KIB/KIR untuk gudang pusat
            if ($filter == 'kir') {
                // Gudang pusat tidak punya KIR, jadi kosongkan query
                $query->whereRaw('1 = 0');
            } elseif ($filter == 'kib') {
                // KIB: aset di gudang pusat
                $query->whereHas('inventory.gudang', function($q) {
                    $q->where('jenis_gudang', 'PUSAT');
                });
            }
            // Jika filter 'semua', tampilkan semua aset di gudang pusat (KIB)
            
            $title = $gudangPusat->nama_gudang;
            $isPusat = true;
        } else {
            // Ambil data gudang unit
            $gudangUnit = MasterGudang::where('id_gudang', $unitKerjaId)
                ->where('jenis_gudang', 'UNIT')
                ->with('unitKerja')
                ->firstOrFail();
            
            // Untuk gudang unit, ambil RegisterAset berdasarkan InventoryItem yang id_gudang = gudang unit
            // KIR adalah aset yang sudah didistribusikan ke gudang unit (InventoryItem.id_gudang = gudang unit)
            $query = RegisterAset::with([
                'inventory.dataBarang',
                'inventory.gudang',
                'inventory.gudang.unitKerja',
                'unitKerja'
            ])->whereHas('inventory', function($q) {
                $q->where('jenis_inventory', 'ASET');
            });
            
            // Filter KIB/KIR untuk gudang unit
            if ($filter == 'kir') {
                // KIR: aset yang sudah didistribusikan ke gudang unit ini (InventoryItem.id_gudang = gudang unit)
                $query->whereHas('inventory.inventoryItems', function($q) use ($gudangUnit) {
                    $q->where('id_gudang', $gudangUnit->id_gudang);
                });
            } elseif ($filter == 'kib') {
                // KIB: aset yang masih di gudang pusat (belum didistribusikan)
                // Untuk gudang unit, KIB adalah semua aset di gudang pusat
                $gudangPusat = MasterGudang::where('jenis_gudang', 'PUSAT')
                    ->where('kategori_gudang', 'ASET')
                    ->first();
                if ($gudangPusat) {
                    $query->whereHas('inventory', function($q) use ($gudangPusat) {
                        $q->where('id_gudang', $gudangPusat->id_gudang);
                    });
                } else {
                    $query->whereRaw('1 = 0'); // Tidak ada gudang pusat
                }
            } else {
                // Filter 'semua': tampilkan semua aset (baik KIB maupun KIR)
                // KIR: aset yang sudah didistribusikan ke gudang unit ini
                // KIB: aset yang masih di gudang pusat
                $gudangPusat = MasterGudang::where('jenis_gudang', 'PUSAT')
                    ->where('kategori_gudang', 'ASET')
                    ->first();
                
                if ($gudangPusat) {
                    $query->where(function($q) use ($gudangUnit, $gudangPusat) {
                        // KIR: InventoryItem.id_gudang = gudang unit
                        $q->whereHas('inventory.inventoryItems', function($q2) use ($gudangUnit) {
                            $q2->where('id_gudang', $gudangUnit->id_gudang);
                        })
                        // KIB: inventory.id_gudang = gudang pusat
                        ->orWhereHas('inventory', function($q2) use ($gudangPusat) {
                            $q2->where('id_gudang', $gudangPusat->id_gudang);
                        });
                    });
                } else {
                    // Hanya KIR jika tidak ada gudang pusat
                    $query->whereHas('inventory.inventoryItems', function($q) use ($gudangUnit) {
                        $q->where('id_gudang', $gudangUnit->id_gudang);
                    });
                }
            }
            
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
            $query->whereHas('inventory', function($q) use ($request) {
                $q->where('id_gudang', $request->id_gudang);
            });
        }
        
        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $registerAsets = $query->latest()->paginate($perPage)->appends($request->query());
        
        // Ambil data untuk dropdown filter
        $gudangs = MasterGudang::where('kategori_gudang', 'ASET')
            ->with('unitKerja')
            ->orderBy('nama_gudang')
            ->get();
        
        return view('asset.register-aset.unit-kerja.show', compact('registerAsets', 'title', 'filter', 'unitKerjaId', 'gudangs', 'isPusat'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Hanya admin dan admin_gudang yang bisa create
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        // Ambil inventory dengan jenis ASET yang belum memiliki register aset
        $inventories = DataInventory::where('jenis_inventory', 'ASET')
            ->whereDoesntHave('registerAset')
            ->with(['dataBarang', 'gudang'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Ambil semua unit kerja
        $unitKerjas = MasterUnitKerja::orderBy('nama_unit_kerja')->get();
        
        return view('asset.register-aset.create', compact('inventories', 'unitKerjas'));
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
            'nomor_register' => 'required|string|max:100|unique:register_aset,nomor_register',
            'kondisi_aset' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT',
            'status_aset' => 'required|in:AKTIF,NONAKTIF',
            'tanggal_perolehan' => 'required|date',
        ]);
        
        // Cek apakah inventory adalah jenis ASET
        $inventory = DataInventory::findOrFail($validated['id_inventory']);
        if ($inventory->jenis_inventory !== 'ASET') {
            return back()->withErrors(['id_inventory' => 'Inventory yang dipilih harus berjenis ASET'])->withInput();
        }
        
        // Cek apakah sudah ada register aset untuk inventory ini
        $existingRegister = RegisterAset::where('id_inventory', $validated['id_inventory'])->first();
        if ($existingRegister) {
            return back()->withErrors(['id_inventory' => 'Register aset untuk inventory ini sudah ada'])->withInput();
        }
        
        // Buat register aset
        $registerAset = RegisterAset::create($validated);
        
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
        $registerAset = RegisterAset::with(['inventory.dataBarang', 'unitKerja'])->findOrFail($id);
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

        return view('asset.register-aset.edit', compact('registerAset'));
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
            'kondisi_aset' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT',
            'status_aset' => 'required|in:AKTIF,NONAKTIF',
            'tanggal_perolehan' => 'nullable|date',
        ]);

        // Update register aset
        $registerAset->update($validated);

        return redirect()->route('asset.register-aset.show', $registerAset->id_register_aset)
            ->with('success', 'Register aset berhasil diperbarui.');
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
}

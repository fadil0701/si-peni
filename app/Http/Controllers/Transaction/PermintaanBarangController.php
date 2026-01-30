<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use App\Models\MasterUnitKerja;
use App\Models\MasterPegawai;
use App\Models\MasterDataBarang;
use App\Models\MasterSatuan;
use App\Models\DataStock;
use App\Models\DataInventory;
use App\Models\RegisterAset;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PermintaanBarangController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PermintaanBarang::with([
            'unitKerja.gudang', // Load gudang unit melalui unit kerja
            'pemohon.jabatan' // Load jabatan pemohon
        ]);

        // Filter berdasarkan unit kerja user yang login untuk pegawai/kepala_unit
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                // Hanya tampilkan permintaan dari unit kerja user yang login
                $query->where('id_unit_kerja', $pegawai->id_unit_kerja);
                // Hanya tampilkan unit kerja user yang login di dropdown
                $unitKerjas = MasterUnitKerja::where('id_unit_kerja', $pegawai->id_unit_kerja)->get();
            } else {
                // Jika user tidak memiliki unit kerja, tidak tampilkan data
                $query->whereRaw('1 = 0');
                $unitKerjas = collect([]);
            }
        } else {
            // Admin dan Admin Gudang melihat semua
            $unitKerjas = MasterUnitKerja::all();
        }

        // Filters
        if ($request->filled('unit_kerja')) {
            $query->where('id_unit_kerja', $request->unit_kerja);
        }

        if ($request->filled('status')) {
            $query->where('status_permintaan', $request->status);
        }

        if ($request->filled('jenis')) {
            // Filter berdasarkan jenis_permintaan yang sekarang berupa JSON array
            $query->whereJsonContains('jenis_permintaan', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_permintaan', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', function($q) use ($search) {
                      $q->where('nama_pegawai', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan tanggal mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_mulai);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_akhir);
        }

        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $permintaans = $query->latest('tanggal_permintaan')->paginate($perPage)->appends($request->query());

        return view('transaction.permintaan-barang.index', compact('permintaans', 'unitKerjas'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Filter unit kerja dan pegawai berdasarkan unit kerja user yang login
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                // Hanya tampilkan unit kerja user yang login
                $unitKerjas = MasterUnitKerja::where('id_unit_kerja', $pegawai->id_unit_kerja)->get();
                // Hanya tampilkan pegawai dari unit kerja yang sama
                $pegawais = MasterPegawai::where('id_unit_kerja', $pegawai->id_unit_kerja)->get();
            } else {
                $unitKerjas = collect([]);
                $pegawais = collect([]);
            }
        } else {
            // Admin dan Admin Gudang melihat semua
            $unitKerjas = MasterUnitKerja::all();
            $pegawais = MasterPegawai::all();
        }
        
        $satuans = MasterSatuan::all();

        // Data barang HANYA dari Data Inventory (filter per jenis)
        // ASET: yang punya aset tersedia = belum didistribusikan ke unit pemohon (masih di gudang)
        // Termasuk: belum punya register, register id_unit_kerja null, atau register id_unit_kerja = unit kerja gudang
        $inventoryAsetIds = DataInventory::where('jenis_inventory', 'ASET')
            ->where('status_inventory', 'AKTIF')
            ->where(function($q) {
                $q->whereDoesntHave('registerAset')
                    ->orWhereHas('registerAset', function($subQ) {
                        $subQ->whereNull('id_unit_kerja')
                            ->orWhereRaw('register_aset.id_unit_kerja = (SELECT id_unit_kerja FROM master_gudang WHERE master_gudang.id_gudang = data_inventory.id_gudang LIMIT 1)');
                    });
            })
            ->whereHas('inventoryItems', function($q) {
                $q->where(function($subQ) {
                    $subQ->where('kondisi_item', 'BAIK')->orWhereNull('kondisi_item');
                })->where('status_item', 'AKTIF');
            })
            ->pluck('id_data_barang')
            ->unique()
            ->values()
            ->toArray();

        // PERSEDIAAN & FARMASI: barang yang ada di Data Inventory (jenis sama)
        $stockPersediaanIds = DataInventory::where('jenis_inventory', 'PERSEDIAAN')
            ->where('status_inventory', 'AKTIF')
            ->pluck('id_data_barang')
            ->unique()
            ->values()
            ->toArray();

        $stockFarmasiIds = DataInventory::where('jenis_inventory', 'FARMASI')
            ->where('status_inventory', 'AKTIF')
            ->pluck('id_data_barang')
            ->unique()
            ->values()
            ->toArray();

        // Dropdown Data Barang hanya berisi barang yang ada di Data Inventory (gabungan semua jenis)
        $inventoryBarangIds = array_unique(array_merge(
            array_map('intval', $inventoryAsetIds),
            array_map('intval', $stockPersediaanIds),
            array_map('intval', $stockFarmasiIds)
        ));
        $dataBarangs = $inventoryBarangIds
            ? MasterDataBarang::with(['subjenisBarang', 'satuan'])->whereIn('id_data_barang', $inventoryBarangIds)->orderBy('kode_data_barang')->get()
            : collect();

        // Stock data: ASET = aset_available (tanpa validasi stock), PERSEDIAAN/FARMASI = stock gudang pusat saja (wajib validasi)
        $stockPersediaanIdsInt = array_map('intval', $stockPersediaanIds);
        $stockFarmasiIdsInt = array_map('intval', $stockFarmasiIds);
        $stockData = [];
        foreach ($dataBarangs as $barang) {
            $key = (string) $barang->id_data_barang;
            $idBarang = (int) $barang->id_data_barang;
            // ASET: jumlah inventory_item yang masih di gudang pusat (belum didistribusikan)
            // Stock dihitung langsung dari inventory_item yang masih di gudang pusat aset
            $asetAvailable = 0;
            if (in_array($idBarang, array_map('intval', $inventoryAsetIds))) {
                // Hitung inventory_item yang masih di gudang pusat aset (belum didistribusikan)
                // Kriteria: 
                // 1. inventory_item dengan status AKTIF dan kondisi BAIK
                // 2. dari data_inventory yang AKTIF
                // 3. gudang adalah gudang pusat dengan kategori ASET
                // 4. belum memiliki register_aset dengan id_unit_kerja yang berbeda dari unit kerja gudang
                $asetAvailable = (int) DB::table('inventory_item')
                    ->join('data_inventory', 'inventory_item.id_inventory', '=', 'data_inventory.id_inventory')
                    ->join('master_gudang', 'data_inventory.id_gudang', '=', 'master_gudang.id_gudang')
                    ->where('data_inventory.id_data_barang', $barang->id_data_barang)
                    ->where('data_inventory.jenis_inventory', 'ASET')
                    ->where('data_inventory.status_inventory', 'AKTIF')
                    ->where('master_gudang.jenis_gudang', 'PUSAT')
                    ->where('master_gudang.kategori_gudang', 'ASET')
                    ->where('inventory_item.status_item', 'AKTIF')
                    ->where(function($q) {
                        $q->where('inventory_item.kondisi_item', 'BAIK')
                          ->orWhereNull('inventory_item.kondisi_item');
                    })
                    ->whereNotExists(function($query) {
                        $query->select(DB::raw(1))
                            ->from('register_aset')
                            ->join('master_gudang as gudang_aset', function($join) {
                                $join->on('gudang_aset.id_unit_kerja', '=', 'register_aset.id_unit_kerja');
                            })
                            ->whereColumn('register_aset.id_inventory', 'data_inventory.id_inventory')
                            ->whereNotNull('register_aset.id_unit_kerja')
                            ->whereColumn('gudang_aset.id_unit_kerja', '!=', 'master_gudang.id_unit_kerja')
                            ->where('register_aset.status_aset', 'AKTIF');
                    })
                    ->count();
            }
            // PERSEDIAAN/FARMASI: stock di gudang pusat saja (Gudang Persediaan / Gudang Farmasi)
            $stockPusatPersediaan = in_array($idBarang, $stockPersediaanIdsInt)
                ? (float) DataStock::getStockGudangPusat($barang->id_data_barang, 'PERSEDIAAN') : 0;
            $stockPusatFarmasi = in_array($idBarang, $stockFarmasiIdsInt)
                ? (float) DataStock::getStockGudangPusat($barang->id_data_barang, 'FARMASI') : 0;

            $stockData[$key] = [
                'total' => (float) DataStock::getTotalStock($barang->id_data_barang),
                'aset_available' => (int) $asetAvailable,
                'stock_gudang_pusat_persediaan' => $stockPusatPersediaan,
                'stock_gudang_pusat_farmasi' => $stockPusatFarmasi,
                'per_gudang' => DataStock::getStockPerGudangPusat($barang->id_data_barang), // Hanya gudang pusat Farmasi/Persediaan
            ];
        }

        return view('transaction.permintaan-barang.create', compact(
            'unitKerjas', 
            'pegawais', 
            'dataBarangs', 
            'satuans', 
            'stockData',
            'inventoryAsetIds',
            'stockPersediaanIds',
            'stockFarmasiIds'
        ));
    }

    public function store(Request $request)
    {
        // Debug: Log request data
        \Log::info('Store Permintaan Request:', [
            'all' => $request->all(),
            'jenis_permintaan' => $request->jenis_permintaan,
            'detail' => $request->detail,
        ]);

        try {
            $validated = $request->validate([
                'id_unit_kerja' => 'required|exists:master_unit_kerja,id_unit_kerja',
                'id_pemohon' => 'required|exists:master_pegawai,id',
                'tanggal_permintaan' => 'required|date',
                'tipe_permintaan' => 'required|in:RUTIN,CITO',
                'jenis_permintaan' => 'required|array|min:1',
                'jenis_permintaan.*' => 'required|in:ASET,PERSEDIAAN,FARMASI',
                'keterangan' => 'nullable|string',
                'detail' => 'required|array|min:1',
                'detail.*.id_data_barang' => 'required|exists:master_data_barang,id_data_barang',
                'detail.*.qty_diminta' => 'required|numeric|min:0.01',
                'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
                'detail.*.keterangan' => 'nullable|string',
            ], [
                'tipe_permintaan.required' => 'Tipe permintaan harus dipilih (Rutin atau CITO (Penting)).',
                'tipe_permintaan.in' => 'Tipe permintaan harus Rutin atau CITO (Penting).',
                'jenis_permintaan.required' => 'Sub jenis permintaan harus dipilih minimal satu (Aset, Persediaan, atau Farmasi).',
                'jenis_permintaan.array' => 'Sub jenis permintaan harus berupa array.',
                'jenis_permintaan.min' => 'Sub jenis permintaan harus dipilih minimal satu.',
                'jenis_permintaan.*.in' => 'Sub jenis permintaan harus Aset, Persediaan, atau Farmasi.',
                'detail.required' => 'Detail permintaan harus diisi.',
                'detail.array' => 'Detail permintaan harus berupa array.',
                'detail.min' => 'Detail permintaan harus diisi minimal satu item.',
                'detail.*.id_data_barang.required' => 'Data barang harus dipilih.',
                'detail.*.qty_diminta.required' => 'Jumlah yang diminta harus diisi.',
                'detail.*.qty_diminta.numeric' => 'Jumlah yang diminta harus berupa angka.',
                'detail.*.qty_diminta.min' => 'Jumlah yang diminta minimal 0.01.',
                'detail.*.id_satuan.required' => 'Satuan harus dipilih.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);
            return back()->withInput()->withErrors($e->errors());
        }

        // Validasi stock hanya untuk PERSEDIAAN dan FARMASI (stock gudang pusat). ASET tidak divalidasi stock.
        $stockPersediaanIds = DataInventory::where('jenis_inventory', 'PERSEDIAAN')->where('status_inventory', 'AKTIF')->pluck('id_data_barang')->unique()->toArray();
        $stockFarmasiIds = DataInventory::where('jenis_inventory', 'FARMASI')->where('status_inventory', 'AKTIF')->pluck('id_data_barang')->unique()->toArray();
        $stockErrors = [];
        foreach ($validated['detail'] as $index => $detail) {
            $idDataBarang = (int) $detail['id_data_barang'];
            $qtyDiminta = (float) $detail['qty_diminta'];
            $stockPusat = null;
            $labelGudang = '';
            if (in_array($idDataBarang, array_map('intval', $stockFarmasiIds))) {
                $stockPusat = DataStock::getStockGudangPusat($detail['id_data_barang'], 'FARMASI');
                $labelGudang = 'Gudang Farmasi (Pusat)';
            } elseif (in_array($idDataBarang, array_map('intval', $stockPersediaanIds))) {
                $stockPusat = DataStock::getStockGudangPusat($detail['id_data_barang'], 'PERSEDIAAN');
                $labelGudang = 'Gudang Persediaan (Pusat)';
            }
            if ($stockPusat !== null && $qtyDiminta > $stockPusat) {
                $dataBarang = MasterDataBarang::find($detail['id_data_barang']);
                $stockErrors["detail.{$index}.qty_diminta"] = "Jumlah yang diminta ({$qtyDiminta}) melebihi stock di {$labelGudang} ({$stockPusat}) untuk barang {$dataBarang->nama_barang}.";
            }
        }

        if (!empty($stockErrors)) {
            return back()->withInput()->withErrors($stockErrors);
        }

        DB::beginTransaction();
        try {
            // Generate nomor permintaan
            $tahun = Carbon::parse($validated['tanggal_permintaan'])->format('Y');
            $lastPermintaan = PermintaanBarang::whereYear('tanggal_permintaan', $tahun)
                ->orderBy('no_permintaan', 'desc')
                ->first();

            $urut = 1;
            if ($lastPermintaan) {
                $parts = explode('/', $lastPermintaan->no_permintaan);
                $urut = (int)end($parts) + 1;
            }

            $noPermintaan = sprintf('PMT/%s/%04d', $tahun, $urut);

            // Create permintaan
            // Simpan jenis_permintaan sebagai JSON array yang berisi sub jenis (ASET, PERSEDIAAN, FARMASI)
            // Tipe permintaan (RUTIN/TAHUNAN) disimpan di kolom terpisah
            $permintaan = PermintaanBarang::create([
                'no_permintaan' => $noPermintaan,
                'id_unit_kerja' => $validated['id_unit_kerja'],
                'id_pemohon' => $validated['id_pemohon'],
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'tipe_permintaan' => $validated['tipe_permintaan'], // RUTIN atau CITO
                'jenis_permintaan' => json_encode($validated['jenis_permintaan']), // Simpan sebagai JSON: ["ASET", "PERSEDIAAN", "FARMASI"]
                'status_permintaan' => 'DRAFT',
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Create detail permintaan
            foreach ($validated['detail'] as $detail) {
                DetailPermintaanBarang::create([
                    'id_permintaan' => $permintaan->id_permintaan,
                    'id_data_barang' => $detail['id_data_barang'],
                    'qty_diminta' => $detail['qty_diminta'],
                    'id_satuan' => $detail['id_satuan'],
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('transaction.permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating permintaan barang: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $permintaan = PermintaanBarang::with(['unitKerja', 'pemohon.jabatan', 'detailPermintaan.dataBarang', 'detailPermintaan.satuan', 'approval'])
            ->findOrFail($id);

        return view('transaction.permintaan-barang.show', compact('permintaan'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $permintaan = PermintaanBarang::with('detailPermintaan')->findOrFail($id);
        
        // Hanya bisa edit jika status DRAFT
        if ($permintaan->status_permintaan !== 'DRAFT') {
            return redirect()->route('transaction.permintaan-barang.show', $id)
                ->with('error', 'Permintaan yang sudah diajukan tidak dapat di-edit.');
        }

        // Filter unit kerja dan pegawai berdasarkan unit kerja user yang login
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                // Hanya tampilkan unit kerja user yang login
                $unitKerjas = MasterUnitKerja::where('id_unit_kerja', $pegawai->id_unit_kerja)->get();
                // Hanya tampilkan pegawai dari unit kerja yang sama
                $pegawais = MasterPegawai::where('id_unit_kerja', $pegawai->id_unit_kerja)->get();
            } else {
                $unitKerjas = collect([]);
                $pegawais = collect([]);
            }
        } else {
            // Admin dan Admin Gudang melihat semua
            $unitKerjas = MasterUnitKerja::all();
            $pegawais = MasterPegawai::all();
        }

        // Data barang HANYA dari Data Inventory (filter per jenis) - sama seperti create
        $inventoryAsetIds = DataInventory::where('jenis_inventory', 'ASET')
            ->where('status_inventory', 'AKTIF')
            ->where(function($q) {
                $q->whereDoesntHave('registerAset')
                    ->orWhereHas('registerAset', function($subQ) {
                        $subQ->whereNull('id_unit_kerja')
                            ->orWhereRaw('register_aset.id_unit_kerja = (SELECT id_unit_kerja FROM master_gudang WHERE master_gudang.id_gudang = data_inventory.id_gudang LIMIT 1)');
                    });
            })
            ->whereHas('inventoryItems', function($q) {
                $q->where(function($subQ) {
                    $subQ->where('kondisi_item', 'BAIK')->orWhereNull('kondisi_item');
                })->where('status_item', 'AKTIF');
            })
            ->pluck('id_data_barang')
            ->unique()
            ->values()
            ->toArray();

        $stockPersediaanIds = DataInventory::where('jenis_inventory', 'PERSEDIAAN')
            ->where('status_inventory', 'AKTIF')
            ->pluck('id_data_barang')
            ->unique()
            ->values()
            ->toArray();

        $stockFarmasiIds = DataInventory::where('jenis_inventory', 'FARMASI')
            ->where('status_inventory', 'AKTIF')
            ->pluck('id_data_barang')
            ->unique()
            ->values()
            ->toArray();

        $inventoryBarangIds = array_unique(array_merge(
            array_map('intval', $inventoryAsetIds),
            array_map('intval', $stockPersediaanIds),
            array_map('intval', $stockFarmasiIds)
        ));
        $dataBarangs = $inventoryBarangIds
            ? MasterDataBarang::with(['subjenisBarang', 'satuan'])->whereIn('id_data_barang', $inventoryBarangIds)->orderBy('kode_data_barang')->get()
            : collect();

        // Stock data: ASET = aset_available, PERSEDIAAN/FARMASI = stock gudang pusat
        $stockPersediaanIdsInt = array_map('intval', $stockPersediaanIds);
        $stockFarmasiIdsInt = array_map('intval', $stockFarmasiIds);
        $stockData = [];
        foreach ($dataBarangs as $barang) {
            $key = (string) $barang->id_data_barang;
            $idBarang = (int) $barang->id_data_barang;
            // ASET: jumlah inventory_item yang masih di gudang pusat (belum didistribusikan)
            $asetAvailable = 0;
            if (in_array($idBarang, array_map('intval', $inventoryAsetIds))) {
                // Hitung inventory_item yang masih di gudang pusat aset (belum didistribusikan)
                // Kriteria: inventory_item dari inventory yang masih di gudang pusat aset dan belum didistribusikan
                $asetAvailable = (int) DB::table('inventory_item')
                    ->join('data_inventory', 'inventory_item.id_inventory', '=', 'data_inventory.id_inventory')
                    ->join('master_gudang', 'data_inventory.id_gudang', '=', 'master_gudang.id_gudang')
                    ->where('data_inventory.id_data_barang', $barang->id_data_barang)
                    ->where('data_inventory.jenis_inventory', 'ASET')
                    ->where('data_inventory.status_inventory', 'AKTIF')
                    ->where('master_gudang.jenis_gudang', 'PUSAT')
                    ->where('master_gudang.kategori_gudang', 'ASET')
                    ->where('inventory_item.status_item', 'AKTIF')
                    ->where(function($q) {
                        $q->where('inventory_item.kondisi_item', 'BAIK')
                          ->orWhereNull('inventory_item.kondisi_item');
                    })
                    ->whereNotExists(function($query) {
                        $query->select(DB::raw(1))
                            ->from('register_aset')
                            ->join('master_gudang as gudang_aset', function($join) {
                                $join->on('gudang_aset.id_unit_kerja', '=', 'register_aset.id_unit_kerja');
                            })
                            ->whereColumn('register_aset.id_inventory', 'data_inventory.id_inventory')
                            ->whereNotNull('register_aset.id_unit_kerja')
                            ->whereColumn('gudang_aset.id_unit_kerja', '!=', 'master_gudang.id_unit_kerja')
                            ->where('register_aset.status_aset', 'AKTIF');
                    })
                    ->count();
            }
            $stockPusatPersediaan = in_array($idBarang, $stockPersediaanIdsInt)
                ? (float) DataStock::getStockGudangPusat($barang->id_data_barang, 'PERSEDIAAN') : 0;
            $stockPusatFarmasi = in_array($idBarang, $stockFarmasiIdsInt)
                ? (float) DataStock::getStockGudangPusat($barang->id_data_barang, 'FARMASI') : 0;

            $stockData[$key] = [
                'total' => (float) DataStock::getTotalStock($barang->id_data_barang),
                'aset_available' => (int) $asetAvailable,
                'stock_gudang_pusat_persediaan' => $stockPusatPersediaan,
                'stock_gudang_pusat_farmasi' => $stockPusatFarmasi,
                'per_gudang' => DataStock::getStockPerGudangPusat($barang->id_data_barang), // Hanya gudang pusat Farmasi/Persediaan
            ];
        }
        $satuans = MasterSatuan::all();

        return view('transaction.permintaan-barang.edit', compact(
            'permintaan', 
            'unitKerjas', 
            'pegawais', 
            'dataBarangs', 
            'satuans',
            'stockData',
            'inventoryAsetIds',
            'stockPersediaanIds',
            'stockFarmasiIds'
        ));
    }

    public function update(Request $request, $id)
    {
        $permintaan = PermintaanBarang::findOrFail($id);

        // Hanya bisa edit jika status DRAFT
        if ($permintaan->status_permintaan !== 'DRAFT') {
            return redirect()->route('transaction.permintaan-barang.show', $id)
                ->with('error', 'Permintaan yang sudah diajukan tidak dapat di-edit.');
        }

        $validated = $request->validate([
            'id_unit_kerja' => 'required|exists:master_unit_kerja,id_unit_kerja',
            'id_pemohon' => 'required|exists:master_pegawai,id',
            'tanggal_permintaan' => 'required|date',
            'tipe_permintaan' => 'required|in:RUTIN,CITO',
            'jenis_permintaan' => 'required|array|min:1',
            'jenis_permintaan.*' => 'required|in:ASET,PERSEDIAAN,FARMASI',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.id_data_barang' => 'required|exists:master_data_barang,id_data_barang',
            'detail.*.qty_diminta' => 'required|numeric|min:0.01',
            'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
            'detail.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update permintaan
            // Simpan jenis_permintaan sebagai JSON array
            $permintaan->update([
                'id_unit_kerja' => $validated['id_unit_kerja'],
                'id_pemohon' => $validated['id_pemohon'],
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'tipe_permintaan' => $validated['tipe_permintaan'], // RUTIN atau CITO
                'jenis_permintaan' => json_encode($validated['jenis_permintaan']), // Simpan sebagai JSON
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Delete existing details
            $permintaan->detailPermintaan()->delete();

            // Create new details
            foreach ($validated['detail'] as $detail) {
                DetailPermintaanBarang::create([
                    'id_permintaan' => $permintaan->id_permintaan,
                    'id_data_barang' => $detail['id_data_barang'],
                    'qty_diminta' => $detail['qty_diminta'],
                    'id_satuan' => $detail['id_satuan'],
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('transaction.permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating permintaan barang: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $permintaan = PermintaanBarang::findOrFail($id);

        // Hanya bisa hapus jika status DRAFT
        if ($permintaan->status_permintaan !== 'DRAFT') {
            return redirect()->route('transaction.permintaan-barang.index')
                ->with('error', 'Permintaan yang sudah diajukan tidak dapat dihapus.');
        }

        $permintaan->detailPermintaan()->delete();
        $permintaan->delete();

        return redirect()->route('transaction.permintaan-barang.index')
            ->with('success', 'Permintaan barang berhasil dihapus.');
    }

    public function ajukan($id)
    {
        $permintaan = PermintaanBarang::findOrFail($id);

        if ($permintaan->status_permintaan !== 'DRAFT') {
            return redirect()->route('transaction.permintaan-barang.show', $id)
                ->with('error', 'Permintaan sudah diajukan sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Update status permintaan
            $permintaan->update(['status_permintaan' => 'DIAJUKAN']);

            // Buat ApprovalLog untuk step pertama (Kepala Unit - Mengetahui)
            // Step 1: Diajukan oleh pegawai (otomatis, tidak perlu approval log)
            // Step 2: Kepala Unit mengetahui
            $flowStep2 = \App\Models\ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('step_order', 2)
                ->first();
            
            if (!$flowStep2) {
                \Log::error('ApprovalFlowDefinition step 2 tidak ditemukan untuk PERMINTAAN_BARANG');
                throw new \Exception('Konfigurasi approval flow tidak ditemukan. Silakan jalankan seeder ApprovalFlowDefinitionSeeder.');
            }
            
            \App\Models\ApprovalLog::create([
                'modul_approval' => 'PERMINTAAN_BARANG',
                'id_referensi' => $permintaan->id_permintaan,
                'id_approval_flow' => $flowStep2->id,
                'user_id' => null, // Akan diisi saat kepala unit mengetahui
                'role_id' => $flowStep2->role_id,
                'status' => 'MENUNGGU',
                'catatan' => null,
                'approved_at' => null,
            ]);
            
            \Log::info('ApprovalLog created:', [
                'id_referensi' => $permintaan->id_permintaan,
                'id_approval_flow' => $flowStep2->id,
                'role_id' => $flowStep2->role_id,
            ]);

            DB::commit();

            return redirect()->route('transaction.permintaan-barang.show', $id)
                ->with('success', 'Permintaan berhasil diajukan untuk persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error mengajukan permintaan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'permintaan_id' => $id,
            ]);
            return redirect()->route('transaction.permintaan-barang.show', $id)
                ->with('error', 'Terjadi kesalahan saat mengajukan permintaan: ' . $e->getMessage());
        }
    }
}

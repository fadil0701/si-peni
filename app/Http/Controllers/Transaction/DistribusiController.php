<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiDistribusi;
use App\Models\DetailDistribusi;
use App\Models\PermintaanBarang;
use App\Models\MasterGudang;
use App\Models\MasterPegawai;
use App\Models\DataInventory;
use App\Models\MasterSatuan;
use Carbon\Carbon;

class DistribusiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = TransaksiDistribusi::with(['gudangAsal', 'gudangTujuan', 'permintaan', 'pegawaiPengirim']);

        // Filter berdasarkan kategori gudang jika user adalah admin gudang kategori spesifik
        if ($user->hasRole('admin_gudang_aset')) {
            $gudangAsetIds = MasterGudang::where('kategori_gudang', 'ASET')->pluck('id_gudang');
            $query->whereIn('id_gudang_asal', $gudangAsetIds);
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $gudangPersediaanIds = MasterGudang::where('kategori_gudang', 'PERSEDIAAN')->pluck('id_gudang');
            $query->whereIn('id_gudang_asal', $gudangPersediaanIds);
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $gudangFarmasiIds = MasterGudang::where('kategori_gudang', 'FARMASI')->pluck('id_gudang');
            $query->whereIn('id_gudang_asal', $gudangFarmasiIds);
        }

        // Filters
        if ($request->filled('gudang')) {
            $query->where(function($q) use ($request) {
                $q->where('id_gudang_asal', $request->gudang)
                  ->orWhere('id_gudang_tujuan', $request->gudang);
            });
        }

        if ($request->filled('status')) {
            $query->where('status_distribusi', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_sbbk', 'like', "%{$search}%")
                  ->orWhereHas('permintaan', function($q) use ($search) {
                      $q->where('no_permintaan', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $distribusis = $query->latest('tanggal_distribusi')->paginate($perPage)->appends($request->query());
        
        // Filter gudang untuk dropdown berdasarkan role
        $gudangsQuery = MasterGudang::query();
        if ($user->hasRole('admin_gudang_aset')) {
            $gudangsQuery->where('kategori_gudang', 'ASET');
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $gudangsQuery->where('kategori_gudang', 'PERSEDIAAN');
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $gudangsQuery->where('kategori_gudang', 'FARMASI');
        }
        $gudangs = $gudangsQuery->get();

        return view('transaction.distribusi.index', compact('distribusis', 'gudangs'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Filter permintaan yang sudah disetujui dan belum didistribusikan
        $permintaans = PermintaanBarang::where('status_permintaan', 'DISETUJUI')
            ->whereDoesntHave('transaksiDistribusi', function($q) {
                $q->whereIn('status_distribusi', ['DRAFT', 'DIKIRIM', 'SELESAI']);
            })
            ->with(['unitKerja', 'pemohon', 'detailPermintaan.dataBarang'])
            ->get();

        // Filter gudang berdasarkan role user
        $gudangsQuery = MasterGudang::query();
        
        // Jika user adalah admin gudang kategori spesifik, filter gudang sesuai kategori
        if ($user->hasRole('admin_gudang_aset')) {
            $gudangsQuery->where('kategori_gudang', 'ASET');
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $gudangsQuery->where('kategori_gudang', 'PERSEDIAAN');
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $gudangsQuery->where('kategori_gudang', 'FARMASI');
        } elseif (!$user->hasRole('admin')) {
            // Untuk role lain selain admin, hanya tampilkan gudang yang sesuai
            // (bisa ditambahkan filter lebih lanjut jika diperlukan)
        }
        
        $gudangs = $gudangsQuery->get();
        $pegawais = MasterPegawai::all();
        $satuans = MasterSatuan::all();

        // Jika ada permintaan_id di request, load detail permintaan
        $selectedPermintaan = null;
        if ($request->filled('permintaan_id')) {
            $selectedPermintaan = PermintaanBarang::with(['detailPermintaan.dataBarang', 'detailPermintaan.satuan'])
                ->find($request->permintaan_id);
        }

        return view('transaction.distribusi.create', compact('permintaans', 'gudangs', 'pegawais', 'satuans', 'selectedPermintaan'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'id_permintaan' => 'required|exists:permintaan_barang,id_permintaan',
            'tanggal_distribusi' => 'required|date',
            'id_gudang_asal' => 'required|exists:master_gudang,id_gudang',
            'id_gudang_tujuan' => 'required|exists:master_gudang,id_gudang|different:id_gudang_asal',
            'id_pegawai_pengirim' => 'required|exists:master_pegawai,id',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.id_inventory' => 'required|exists:data_inventory,id_inventory',
            'detail.*.qty_distribusi' => 'required|numeric|min:0.01',
            'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
            'detail.*.harga_satuan' => 'required|numeric|min:0',
            'detail.*.keterangan' => 'nullable|string',
        ]);
        
        // Validasi: Pastikan gudang asal sesuai dengan kategori admin gudang
        if (!$user->hasRole('admin')) {
            $gudangAsal = MasterGudang::find($validated['id_gudang_asal']);
            
            if ($gudangAsal) {
                $allowed = false;
                if ($user->hasRole('admin_gudang_aset') && $gudangAsal->kategori_gudang === 'ASET') {
                    $allowed = true;
                } elseif ($user->hasRole('admin_gudang_persediaan') && $gudangAsal->kategori_gudang === 'PERSEDIAAN') {
                    $allowed = true;
                } elseif ($user->hasRole('admin_gudang_farmasi') && $gudangAsal->kategori_gudang === 'FARMASI') {
                    $allowed = true;
                } elseif ($user->hasRole('admin_gudang')) {
                    $allowed = true; // Admin gudang umum bisa akses semua
                }
                
                if (!$allowed) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Anda tidak memiliki hak untuk melakukan distribusi dari gudang kategori ' . $gudangAsal->kategori_gudang . '.');
                }
            }
        }

        DB::beginTransaction();
        try {
            // Generate nomor SBBK
            $tahun = Carbon::parse($validated['tanggal_distribusi'])->format('Y');
            $lastDistribusi = TransaksiDistribusi::whereYear('tanggal_distribusi', $tahun)
                ->orderBy('no_sbbk', 'desc')
                ->first();

            $urut = 1;
            if ($lastDistribusi) {
                $parts = explode('/', $lastDistribusi->no_sbbk);
                $urut = (int)end($parts) + 1;
            }

            $noSbbk = sprintf('SBBK/%s/%04d', $tahun, $urut);

            // Create distribusi
            $distribusi = TransaksiDistribusi::create([
                'no_sbbk' => $noSbbk,
                'id_permintaan' => $validated['id_permintaan'],
                'tanggal_distribusi' => $validated['tanggal_distribusi'],
                'id_gudang_asal' => $validated['id_gudang_asal'],
                'id_gudang_tujuan' => $validated['id_gudang_tujuan'],
                'id_pegawai_pengirim' => $validated['id_pegawai_pengirim'],
                'status_distribusi' => 'DRAFT',
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Create detail distribusi
            foreach ($validated['detail'] as $detail) {
                $subtotal = $detail['qty_distribusi'] * $detail['harga_satuan'];
                
                DetailDistribusi::create([
                    'id_distribusi' => $distribusi->id_distribusi,
                    'id_inventory' => $detail['id_inventory'],
                    'qty_distribusi' => $detail['qty_distribusi'],
                    'id_satuan' => $detail['id_satuan'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $subtotal,
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('transaction.distribusi.index')
                ->with('success', 'Distribusi barang berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating distribusi: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $distribusi = TransaksiDistribusi::with([
            'permintaan.unitKerja',
            'permintaan.pemohon',
            'gudangAsal',
            'gudangTujuan',
            'pegawaiPengirim',
            'detailDistribusi.inventory.dataBarang',
            'detailDistribusi.satuan'
        ])->findOrFail($id);

        return view('transaction.distribusi.show', compact('distribusi'));
    }

    public function edit($id)
    {
        $distribusi = TransaksiDistribusi::with('detailDistribusi')->findOrFail($id);
        
        // Hanya bisa edit jika status DRAFT
        if ($distribusi->status_distribusi !== 'DRAFT') {
            return redirect()->route('transaction.distribusi.show', $id)
                ->with('error', 'Distribusi yang sudah dikirim tidak dapat di-edit.');
        }

        $permintaans = PermintaanBarang::where('status_permintaan', 'DISETUJUI')->get();
        $gudangs = MasterGudang::all();
        $pegawais = MasterPegawai::all();
        $satuans = MasterSatuan::all();
        
        // Load inventories dari gudang asal
        $inventories = DataInventory::where('id_gudang', $distribusi->id_gudang_asal)
            ->with('dataBarang')
            ->get();

        return view('transaction.distribusi.edit', compact('distribusi', 'permintaans', 'gudangs', 'pegawais', 'satuans', 'inventories'));
    }

    public function update(Request $request, $id)
    {
        $distribusi = TransaksiDistribusi::findOrFail($id);

        // Hanya bisa edit jika status DRAFT
        if ($distribusi->status_distribusi !== 'DRAFT') {
            return redirect()->route('transaction.distribusi.show', $id)
                ->with('error', 'Distribusi yang sudah dikirim tidak dapat di-edit.');
        }

        $validated = $request->validate([
            'tanggal_distribusi' => 'required|date',
            'id_gudang_asal' => 'required|exists:master_gudang,id_gudang',
            'id_gudang_tujuan' => 'required|exists:master_gudang,id_gudang|different:id_gudang_asal',
            'id_pegawai_pengirim' => 'required|exists:master_pegawai,id',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.id_inventory' => 'required|exists:data_inventory,id_inventory',
            'detail.*.qty_distribusi' => 'required|numeric|min:0.01',
            'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
            'detail.*.harga_satuan' => 'required|numeric|min:0',
            'detail.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update distribusi
            $distribusi->update([
                'tanggal_distribusi' => $validated['tanggal_distribusi'],
                'id_gudang_asal' => $validated['id_gudang_asal'],
                'id_gudang_tujuan' => $validated['id_gudang_tujuan'],
                'id_pegawai_pengirim' => $validated['id_pegawai_pengirim'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Delete existing details
            $distribusi->detailDistribusi()->delete();

            // Create new details
            foreach ($validated['detail'] as $detail) {
                $subtotal = $detail['qty_distribusi'] * $detail['harga_satuan'];
                
                DetailDistribusi::create([
                    'id_distribusi' => $distribusi->id_distribusi,
                    'id_inventory' => $detail['id_inventory'],
                    'qty_distribusi' => $detail['qty_distribusi'],
                    'id_satuan' => $detail['id_satuan'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $subtotal,
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('transaction.distribusi.index')
                ->with('success', 'Distribusi barang berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating distribusi: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $distribusi = TransaksiDistribusi::findOrFail($id);

        // Hanya bisa hapus jika status DRAFT
        if ($distribusi->status_distribusi !== 'DRAFT') {
            return redirect()->route('transaction.distribusi.index')
                ->with('error', 'Distribusi yang sudah dikirim tidak dapat dihapus.');
        }

        $distribusi->detailDistribusi()->delete();
        $distribusi->delete();

        return redirect()->route('transaction.distribusi.index')
            ->with('success', 'Distribusi barang berhasil dihapus.');
    }

    public function kirim($id)
    {
        $distribusi = TransaksiDistribusi::with('detailDistribusi')->findOrFail($id);

        if ($distribusi->status_distribusi !== 'DRAFT') {
            return redirect()->route('transaction.distribusi.show', $id)
                ->with('error', 'Distribusi sudah dikirim sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Update status distribusi
            $distribusi->update(['status_distribusi' => 'DIKIRIM']);

            // Update stock gudang asal (kurangi) dan tujuan (tambah)
            foreach ($distribusi->detailDistribusi as $detail) {
                $inventory = $detail->inventory;
                
                // Kurangi stock gudang asal
                $stockAsal = \App\Models\DataStock::where('id_data_barang', $inventory->id_data_barang)
                    ->where('id_gudang', $distribusi->id_gudang_asal)
                    ->first();
                
                if ($stockAsal) {
                    $stockAsal->qty_keluar += $detail->qty_distribusi;
                    $stockAsal->qty_akhir -= $detail->qty_distribusi;
                    $stockAsal->last_updated = now();
                    $stockAsal->save();
                }

                // Tambah stock gudang tujuan
                $stockTujuan = \App\Models\DataStock::firstOrNew([
                    'id_data_barang' => $inventory->id_data_barang,
                    'id_gudang' => $distribusi->id_gudang_tujuan,
                ]);

                if ($stockTujuan->exists) {
                    $stockTujuan->qty_masuk += $detail->qty_distribusi;
                    $stockTujuan->qty_akhir += $detail->qty_distribusi;
                } else {
                    $stockTujuan->qty_awal = 0;
                    $stockTujuan->qty_masuk = $detail->qty_distribusi;
                    $stockTujuan->qty_keluar = 0;
                    $stockTujuan->qty_akhir = $detail->qty_distribusi;
                    $stockTujuan->id_satuan = $detail->id_satuan;
                }

                $stockTujuan->last_updated = now();
                $stockTujuan->save();

                // Untuk ASET: Update id_gudang di inventory_item yang didistribusikan
                if ($inventory->jenis_inventory === 'ASET') {
                    // Ambil inventory_item yang masih di gudang asal dan belum didistribusikan
                    $inventoryItems = \App\Models\InventoryItem::where('id_inventory', $inventory->id_inventory)
                        ->where('id_gudang', $distribusi->id_gudang_asal)
                        ->where('status_item', 'AKTIF')
                        ->limit((int)$detail->qty_distribusi)
                        ->get();

                    // Update id_gudang ke gudang tujuan
                    foreach ($inventoryItems as $item) {
                        $item->update(['id_gudang' => $distribusi->id_gudang_tujuan]);
                    }
                } else {
                    // Untuk PERSEDIAAN/FARMASI: Buat inventory baru di gudang tujuan atau update id_gudang
                    // Jika qty_distribusi sama dengan qty_input, pindahkan seluruh inventory
                    // Jika tidak, buat inventory baru di gudang tujuan
                    if ($detail->qty_distribusi >= $inventory->qty_input) {
                        // Pindahkan seluruh inventory ke gudang tujuan
                        $inventory->update(['id_gudang' => $distribusi->id_gudang_tujuan]);
                    } else {
                        // Buat inventory baru di gudang tujuan dengan qty yang didistribusikan
                        $newInventory = $inventory->replicate();
                        $newInventory->id_gudang = $distribusi->id_gudang_tujuan;
                        $newInventory->qty_input = $detail->qty_distribusi;
                        $newInventory->total_harga = $detail->qty_distribusi * $inventory->harga_satuan;
                        $newInventory->status_inventory = 'AKTIF';
                        $newInventory->save();

                        // Kurangi qty di inventory asal
                        $inventory->qty_input -= $detail->qty_distribusi;
                        $inventory->total_harga = $inventory->qty_input * $inventory->harga_satuan;
                        if ($inventory->qty_input <= 0) {
                            $inventory->status_inventory = 'HABIS';
                        }
                        $inventory->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('transaction.distribusi.show', $id)
                ->with('success', 'Distribusi berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error mengirim distribusi: ' . $e->getMessage());
            return redirect()->route('transaction.distribusi.show', $id)
                ->with('error', 'Terjadi kesalahan saat mengirim distribusi: ' . $e->getMessage());
        }
    }

    /**
     * API: Get gudang tujuan berdasarkan permintaan
     */
    public function getGudangTujuanByPermintaan($permintaanId)
    {
        try {
            $permintaan = PermintaanBarang::with('unitKerja')->findOrFail($permintaanId);
            
            // Cari gudang yang memiliki id_unit_kerja sama dengan unit kerja yang melakukan permintaan
            // dan jenis_gudang = 'UNIT'
            $gudangTujuan = MasterGudang::where('id_unit_kerja', $permintaan->id_unit_kerja)
                ->where('jenis_gudang', 'UNIT')
                ->get();
            
            return response()->json([
                'success' => true,
                'gudang' => $gudangTujuan->map(function($gudang) {
                    return [
                        'id_gudang' => $gudang->id_gudang,
                        'nama_gudang' => $gudang->nama_gudang,
                        'jenis_gudang' => $gudang->jenis_gudang,
                        'kategori_gudang' => $gudang->kategori_gudang,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data gudang tujuan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInventoryByGudang($gudangId)
    {
        $inventories = DataInventory::where('id_gudang', $gudangId)
            ->where('status_inventory', 'AKTIF')
            ->with(['dataBarang', 'satuan'])
            ->get();

        $result = $inventories->map(function($inv) {
            // Hitung qty available (qty_input dikurangi yang sudah didistribusikan)
            $qtyDistributed = DetailDistribusi::where('id_inventory', $inv->id_inventory)
                ->whereHas('distribusi', function($q) {
                    $q->whereIn('status_distribusi', ['DRAFT', 'DIKIRIM', 'SELESAI']);
                })
                ->sum('qty_distribusi');
            
            $qtyAvailable = $inv->qty_input - $qtyDistributed;

            return [
                'id_inventory' => $inv->id_inventory,
                'nama_barang' => $inv->dataBarang->nama_barang ?? '-',
                'kode_barang' => $inv->dataBarang->kode_data_barang ?? '-',
                'harga_satuan' => $inv->harga_satuan,
                'id_satuan' => $inv->id_satuan,
                'qty_available' => max(0, $qtyAvailable),
            ];
        })->filter(function($inv) {
            return $inv['qty_available'] > 0; // Hanya tampilkan yang masih ada stok
        });

        return response()->json(['inventory' => $result->values()]);
    }

    public function getPermintaanDetail($id)
    {
        $permintaan = PermintaanBarang::with(['detailPermintaan.dataBarang', 'detailPermintaan.satuan'])
            ->findOrFail($id);

        $details = $permintaan->detailPermintaan->map(function($detail) {
            return [
                'nama_barang' => $detail->dataBarang->nama_barang ?? '-',
                'qty_diminta' => number_format($detail->qty_diminta, 2),
                'satuan' => $detail->satuan->nama_satuan ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'details' => $details,
        ]);
    }
}

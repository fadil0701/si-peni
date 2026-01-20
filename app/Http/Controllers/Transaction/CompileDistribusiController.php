<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftDetailDistribusi;
use App\Models\TransaksiDistribusi;
use App\Models\DetailDistribusi;
use App\Models\PermintaanBarang;
use App\Models\MasterGudang;
use App\Models\MasterPegawai;
use Carbon\Carbon;

class CompileDistribusiController extends Controller
{
    /**
     * Menampilkan daftar permintaan yang siap untuk di-compile menjadi SBBK
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Hanya admin_gudang atau admin yang bisa compile
        if (!$user->hasRole('admin_gudang') && !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki akses untuk compile SBBK.');
        }

        // Ambil permintaan yang sudah memiliki draft detail dari semua kategori dan statusnya READY
        $query = PermintaanBarang::where('status_permintaan', 'DISETUJUI')
            ->whereHas('draftDetailDistribusi', function($q) {
                $q->where('status', 'READY');
            })
            ->with(['unitKerja', 'pemohon', 'detailPermintaan', 'draftDetailDistribusi' => function($q) {
                $q->where('status', 'READY');
            }]);
        
        // Ambil semua data untuk filter
        $allPermintaans = $query->latest('tanggal_permintaan')->get();
        
        // Filter berdasarkan kategori yang sudah ready
        $filteredPermintaans = $allPermintaans->filter(function($permintaan) {
            // Pastikan semua kategori yang diperlukan sudah ready
            $kategoriNeeded = [];
            $jenisPermintaan = is_array($permintaan->jenis_permintaan) 
                ? $permintaan->jenis_permintaan 
                : json_decode($permintaan->jenis_permintaan, true) ?? [];
            
            if (in_array('ASET', $jenisPermintaan)) {
                $kategoriNeeded[] = 'ASET';
            }
            
            // Cek detail permintaan untuk PERSEDIAAN dan FARMASI
            foreach ($permintaan->detailPermintaan as $detail) {
                $inventory = \App\Models\DataInventory::where('id_data_barang', $detail->id_data_barang)->first();
                if ($inventory && in_array($inventory->jenis_inventory, ['PERSEDIAAN', 'FARMASI'])) {
                    if (!in_array($inventory->jenis_inventory, $kategoriNeeded)) {
                        $kategoriNeeded[] = $inventory->jenis_inventory;
                    }
                }
            }
            
            // Cek apakah semua kategori sudah ready
            foreach ($kategoriNeeded as $kategori) {
                $draftReady = $permintaan->draftDetailDistribusi->where('kategori_gudang', $kategori)
                    ->where('status', 'READY')
                    ->count();
                if ($draftReady == 0) {
                    return false; // Belum ready
                }
            }
            
            return true; // Semua kategori ready
        });
        
        // Pagination manual
        $page = $request->get('page', 1);
        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $total = $filteredPermintaans->count();
        $items = $filteredPermintaans->slice(($page - 1) * $perPage, $perPage)->values();
        
        // Buat paginator manual
        $permintaans = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('transaction.compile-distribusi.index', compact('permintaans'));
    }

    /**
     * Menampilkan form untuk compile SBBK dari draft detail
     */
    public function create(Request $request, $permintaanId)
    {
        $user = Auth::user();
        
        // Hanya admin_gudang atau admin yang bisa compile
        if (!$user->hasRole('admin_gudang') && !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki akses untuk compile SBBK.');
        }

        $permintaan = PermintaanBarang::with([
            'unitKerja',
            'pemohon',
            'detailPermintaan.dataBarang',
            'draftDetailDistribusi' => function($q) {
                $q->where('status', 'READY')->with(['inventory.dataBarang', 'gudangAsal', 'satuan']);
            }
        ])->findOrFail($permintaanId);

        // Validasi bahwa semua kategori sudah ready
        $draftDetails = $permintaan->draftDetailDistribusi->where('status', 'READY');
        if ($draftDetails->count() == 0) {
            return redirect()->route('transaction.compile-distribusi.index')
                ->with('error', 'Belum ada draft detail yang ready untuk permintaan ini.');
        }

        // Ambil gudang tujuan (unit kerja yang meminta)
        $gudangTujuan = MasterGudang::where('id_unit_kerja', $permintaan->id_unit_kerja)
            ->where('jenis_gudang', 'UNIT')
            ->first();

        if (!$gudangTujuan) {
            return redirect()->route('transaction.compile-distribusi.index')
                ->with('error', 'Gudang tujuan tidak ditemukan untuk unit kerja ini.');
        }

        $pegawais = MasterPegawai::all();

        return view('transaction.compile-distribusi.create', compact(
            'permintaan',
            'draftDetails',
            'gudangTujuan',
            'pegawais'
        ));
    }

    /**
     * Menyimpan SBBK yang di-compile dari draft detail
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Hanya admin_gudang atau admin yang bisa compile
        if (!$user->hasRole('admin_gudang') && !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki akses untuk compile SBBK.');
        }

        $validated = $request->validate([
            'id_permintaan' => 'required|exists:permintaan_barang,id_permintaan',
            'tanggal_distribusi' => 'required|date',
            'id_gudang_tujuan' => 'required|exists:master_gudang,id_gudang',
            'id_pegawai_pengirim' => 'required|exists:master_pegawai,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Ambil semua draft detail yang ready untuk permintaan ini
            $draftDetails = DraftDetailDistribusi::where('id_permintaan', $validated['id_permintaan'])
                ->where('status', 'READY')
                ->get();

            if ($draftDetails->count() == 0) {
                return back()->withInput()->with('error', 'Tidak ada draft detail yang ready untuk di-compile.');
            }

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

            // Tentukan gudang asal (bisa dari draft detail pertama, atau bisa multiple)
            // Untuk sekarang, kita ambil gudang asal dari draft detail pertama
            // Jika ada multiple gudang asal, bisa di-handle berbeda
            $gudangAsal = $draftDetails->first()->id_gudang_asal;

            // Create distribusi
            $distribusi = TransaksiDistribusi::create([
                'no_sbbk' => $noSbbk,
                'id_permintaan' => $validated['id_permintaan'],
                'tanggal_distribusi' => $validated['tanggal_distribusi'],
                'id_gudang_asal' => $gudangAsal,
                'id_gudang_tujuan' => $validated['id_gudang_tujuan'],
                'id_pegawai_pengirim' => $validated['id_pegawai_pengirim'],
                'status_distribusi' => 'DRAFT',
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Create detail distribusi dari draft detail
            foreach ($draftDetails as $draft) {
                DetailDistribusi::create([
                    'id_distribusi' => $distribusi->id_distribusi,
                    'id_inventory' => $draft->id_inventory,
                    'qty_distribusi' => $draft->qty_distribusi,
                    'id_satuan' => $draft->id_satuan,
                    'harga_satuan' => $draft->harga_satuan,
                    'subtotal' => $draft->subtotal,
                    'keterangan' => $draft->keterangan,
                ]);

                // Update status draft menjadi COMPILED
                $draft->update(['status' => 'COMPILED']);
            }

            DB::commit();

            return redirect()->route('transaction.distribusi.show', $distribusi->id_distribusi)
                ->with('success', 'SBBK berhasil dibuat dari draft detail distribusi.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error compiling distribusi: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}

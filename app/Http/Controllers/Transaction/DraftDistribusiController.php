<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftDetailDistribusi;
use App\Models\PermintaanBarang;
use App\Models\ApprovalLog;
use App\Models\MasterGudang;
use App\Models\DataInventory;
use App\Models\MasterSatuan;

class DraftDistribusiController extends Controller
{
    /**
     * Menampilkan daftar disposisi yang perlu diproses oleh admin gudang kategori
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Tentukan kategori gudang berdasarkan role user
        $kategoriGudang = null;
        if ($user->hasRole('admin_gudang_aset')) {
            $kategoriGudang = 'ASET';
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $kategoriGudang = 'PERSEDIAAN';
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $kategoriGudang = 'FARMASI';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk memproses disposisi.');
        }

        // Ambil approval log yang perlu diproses oleh admin gudang kategori ini
        $roleId = $user->roles()->where('name', 'like', 'admin_gudang_%')->first()?->id;
        
        $approvalLogs = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('role_id', $roleId)
            ->where('status', 'MENUNGGU')
            ->with(['permintaan' => function($q) {
                $q->with(['unitKerja', 'pemohon', 'detailPermintaan.dataBarang']);
            }])
            ->latest()
            ->paginate(\App\Helpers\PaginationHelper::getPerPage($request, 10))->appends($request->query());

        return view('transaction.draft-distribusi.index', compact('approvalLogs', 'kategoriGudang'));
    }

    /**
     * Menampilkan form untuk memproses disposisi
     */
    public function create(Request $request, $approvalLogId)
    {
        $user = Auth::user();
        
        // Tentukan kategori gudang berdasarkan role user
        $kategoriGudang = null;
        if ($user->hasRole('admin_gudang_aset')) {
            $kategoriGudang = 'ASET';
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $kategoriGudang = 'PERSEDIAAN';
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $kategoriGudang = 'FARMASI';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk memproses disposisi.');
        }

        $approvalLog = ApprovalLog::with(['permintaan' => function($q) {
            $q->with(['unitKerja', 'pemohon', 'detailPermintaan.dataBarang']);
        }])
            ->findOrFail($approvalLogId);

        // Validasi bahwa approval log ini untuk role user
        $userRoleId = $user->roles()->where('name', 'like', 'admin_gudang_%')->first()?->id;
        if ($approvalLog->role_id !== $userRoleId || $approvalLog->status !== 'MENUNGGU') {
            abort(403, 'Anda tidak memiliki akses untuk memproses disposisi ini.');
        }

        // Ambil gudang sesuai kategori
        $gudangs = MasterGudang::where('kategori_gudang', $kategoriGudang)
            ->where('jenis_gudang', 'PUSAT')
            ->get();

        // Ambil inventory sesuai kategori dari gudang pusat
        $gudangIds = $gudangs->pluck('id_gudang');
        $inventories = DataInventory::whereIn('id_gudang', $gudangIds)
            ->where('jenis_inventory', $kategoriGudang)
            ->with(['dataBarang', 'satuan'])
            ->get();

        // Filter detail permintaan sesuai kategori
        $detailPermintaan = $approvalLog->permintaan->detailPermintaan->filter(function($detail) use ($kategoriGudang) {
            // Cek apakah barang ini termasuk dalam kategori yang diminta
            $inventory = DataInventory::where('id_data_barang', $detail->id_data_barang)
                ->where('jenis_inventory', $kategoriGudang)
                ->first();
            return $inventory !== null;
        });

        $satuans = MasterSatuan::all();

        return view('transaction.draft-distribusi.create', compact(
            'approvalLog',
            'kategoriGudang',
            'gudangs',
            'inventories',
            'detailPermintaan',
            'satuans'
        ));
    }

    /**
     * Menyimpan draft detail distribusi dari admin gudang kategori
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Tentukan kategori gudang berdasarkan role user
        $kategoriGudang = null;
        if ($user->hasRole('admin_gudang_aset')) {
            $kategoriGudang = 'ASET';
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $kategoriGudang = 'PERSEDIAAN';
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $kategoriGudang = 'FARMASI';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk memproses disposisi.');
        }

        $validated = $request->validate([
            'id_permintaan' => 'required|exists:permintaan_barang,id_permintaan',
            'detail' => 'required|array|min:1',
            'detail.*.id_inventory' => 'required|exists:data_inventory,id_inventory',
            'detail.*.id_gudang_asal' => 'required|exists:master_gudang,id_gudang',
            'detail.*.qty_distribusi' => 'required|numeric|min:0.01',
            'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
            'detail.*.harga_satuan' => 'required|numeric|min:0',
            'detail.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Hapus draft lama jika ada
            DraftDetailDistribusi::where('id_permintaan', $validated['id_permintaan'])
                ->where('kategori_gudang', $kategoriGudang)
                ->where('created_by', $user->id)
                ->where('status', 'DRAFT')
                ->delete();

            // Buat draft detail baru
            foreach ($validated['detail'] as $detail) {
                $subtotal = $detail['qty_distribusi'] * $detail['harga_satuan'];
                
                DraftDetailDistribusi::create([
                    'id_permintaan' => $validated['id_permintaan'],
                    'id_inventory' => $detail['id_inventory'],
                    'id_gudang_asal' => $detail['id_gudang_asal'],
                    'qty_distribusi' => $detail['qty_distribusi'],
                    'id_satuan' => $detail['id_satuan'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $subtotal,
                    'kategori_gudang' => $kategoriGudang,
                    'created_by' => $user->id,
                    'status' => 'READY',
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            // Update approval log menjadi DIPROSES
            $approvalLog = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('id_referensi', $validated['id_permintaan'])
                ->where('role_id', $user->roles()->where('name', 'like', 'admin_gudang_%')->first()?->id)
                ->where('status', 'MENUNGGU')
                ->first();

            if ($approvalLog) {
                $approvalLog->update([
                    'status' => 'DIPROSES',
                    'user_id' => $user->id,
                    'approved_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('transaction.draft-distribusi.index')
                ->with('success', 'Draft detail distribusi berhasil dibuat dan siap untuk di-compile menjadi SBBK.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating draft distribusi: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail draft distribusi berdasarkan approval log
     */
    public function show($approvalLogId)
    {
        $user = Auth::user();
        
        // Tentukan kategori gudang berdasarkan role user
        $kategoriGudang = null;
        if ($user->hasRole('admin_gudang_aset')) {
            $kategoriGudang = 'ASET';
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $kategoriGudang = 'PERSEDIAAN';
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $kategoriGudang = 'FARMASI';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail disposisi.');
        }

        $approvalLog = ApprovalLog::with(['permintaan' => function($q) {
            $q->with(['unitKerja', 'pemohon']);
        }])
            ->findOrFail($approvalLogId);

        // Ambil semua draft detail untuk permintaan ini dengan kategori yang sesuai
        $draftDetails = DraftDetailDistribusi::where('id_permintaan', $approvalLog->id_referensi)
            ->where('kategori_gudang', $kategoriGudang)
            ->where('created_by', $user->id)
            ->with([
                'inventory.dataBarang',
                'gudangAsal',
                'satuan',
                'createdBy'
            ])
            ->get();

        return view('transaction.draft-distribusi.show', compact('approvalLog', 'draftDetails', 'kategoriGudang'));
    }
}

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
use App\Models\Role;

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
        $isAdmin = $user->hasRole('admin') || $user->hasRole('admin_gudang');
        $isViewOnly = $user->hasRole('kepala_unit') || $user->hasRole('kepala_pusat') || $user->hasRole('kasubbag_tu');
        
        if ($isAdmin) {
            // Admin bisa melihat semua kategori - ambil dari filter request jika ada
            $kategoriGudang = $request->get('kategori', null);
        } elseif ($user->hasRole('admin_gudang_aset')) {
            $kategoriGudang = 'ASET';
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $kategoriGudang = 'PERSEDIAAN';
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $kategoriGudang = 'FARMASI';
        } elseif ($isViewOnly) {
            // Kepala unit, kepala pusat, dan kasubbag bisa melihat semua untuk monitoring
            $kategoriGudang = $request->get('kategori', null);
        } else {
            abort(403, 'Anda tidak memiliki akses untuk melihat disposisi.');
        }

        // Ambil approval log yang perlu diproses
        // Hanya tampilkan approval log untuk admin_gudang_aset, admin_gudang_persediaan, admin_gudang_farmasi
        // JANGAN tampilkan approval log untuk admin_gudang (role umum)
        $kategoriRoleIds = \App\Models\Role::whereIn('name', ['admin_gudang_aset', 'admin_gudang_persediaan', 'admin_gudang_farmasi'])
            ->pluck('id')
            ->toArray();
        
        $approvalLogsQuery = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('status', 'MENUNGGU')
            ->whereIn('role_id', $kategoriRoleIds) // Hanya role kategori spesifik
            ->with('approvalFlow.role'); // Eager load role untuk mendapatkan kategori
        
        // Jika bukan admin dan bukan view-only, filter berdasarkan role user
        if (!$isAdmin && !$isViewOnly) {
            $roleId = $user->roles()->where('name', 'like', 'admin_gudang_%')->first()?->id;
            if ($roleId) {
                $approvalLogsQuery->where('role_id', $roleId);
            }
        }
        // Jika admin atau view-only dan ada filter kategori, filter berdasarkan role yang sesuai
        elseif (($isAdmin || $isViewOnly) && $kategoriGudang) {
            $roleMap = [
                'ASET' => 'admin_gudang_aset',
                'PERSEDIAAN' => 'admin_gudang_persediaan',
                'FARMASI' => 'admin_gudang_farmasi',
            ];
            if (isset($roleMap[$kategoriGudang])) {
                $roleId = \App\Models\Role::where('name', $roleMap[$kategoriGudang])->first()?->id;
                if ($roleId) {
                    $approvalLogsQuery->where('role_id', $roleId);
                }
            }
        }
        // Jika admin atau view-only tanpa filter, tampilkan semua kategori (sudah difilter di whereIn di atas)
        
        $approvalLogs = $approvalLogsQuery->latest()
            ->paginate(\App\Helpers\PaginationHelper::getPerPage($request, 10))->appends($request->query());
        
        // Load permintaan untuk setiap approval log secara manual karena relationship conditional
        $approvalLogs->getCollection()->transform(function($log) {
            if ($log->modul_approval === 'PERMINTAAN_BARANG') {
                $log->permintaan = PermintaanBarang::with(['unitKerja', 'pemohon', 'detailPermintaan.dataBarang'])
                    ->find($log->id_referensi);
            }
            return $log;
        });

        return view('transaction.draft-distribusi.index', compact('approvalLogs', 'kategoriGudang', 'isAdmin', 'isViewOnly'));
    }

    /**
     * Menampilkan form untuk memproses disposisi
     */
    public function create(Request $request, $approvalLogId)
    {
        $user = Auth::user();
        
        // Tentukan kategori gudang berdasarkan role user
        $kategoriGudang = null;
        $isAdmin = $user->hasRole('admin') || $user->hasRole('admin_gudang');
        
        if ($isAdmin) {
            // Admin bisa memilih kategori dari request
            $kategoriGudang = $request->get('kategori', 'ASET'); // default ASET jika tidak ada
        } elseif ($user->hasRole('admin_gudang_aset')) {
            $kategoriGudang = 'ASET';
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $kategoriGudang = 'PERSEDIAAN';
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $kategoriGudang = 'FARMASI';
        } else {
            abort(403, 'Anda tidak memiliki akses untuk memproses disposisi.');
        }

        $approvalLog = ApprovalLog::findOrFail($approvalLogId);
        
        // Validasi modul approval
        if ($approvalLog->modul_approval !== 'PERMINTAAN_BARANG') {
            abort(404, 'Approval log ini bukan untuk permintaan barang.');
        }

        // Load permintaan dengan eager loading yang benar
        $permintaan = PermintaanBarang::with(['unitKerja', 'pemohon', 'detailPermintaan.dataBarang'])
            ->find($approvalLog->id_referensi);
        
        if (!$permintaan) {
            abort(404, 'Permintaan barang tidak ditemukan.');
        }

        // Validasi bahwa approval log ini untuk role user (kecuali admin)
        if (!$isAdmin) {
            $userRoleId = $user->roles()->where('name', 'like', 'admin_gudang_%')->first()?->id;
            if ($approvalLog->role_id !== $userRoleId || $approvalLog->status !== 'MENUNGGU') {
                abort(403, 'Anda tidak memiliki akses untuk memproses disposisi ini.');
            }
        } else {
            // Admin harus memastikan approval log status MENUNGGU
            if ($approvalLog->status !== 'MENUNGGU') {
                abort(403, 'Disposisi ini sudah diproses atau dibatalkan.');
            }
        }

        // Ambil gudang sesuai kategori
        $gudangs = MasterGudang::where('kategori_gudang', $kategoriGudang)
            ->where('jenis_gudang', 'PUSAT')
            ->get();

        // Ambil inventory sesuai kategori dari gudang pusat dengan status AKTIF
        $gudangIds = $gudangs->pluck('id_gudang');
        $inventories = DataInventory::whereIn('id_gudang', $gudangIds)
            ->where('jenis_inventory', $kategoriGudang)
            ->where('status_inventory', 'AKTIF')
            ->with(['dataBarang', 'satuan'])
            ->get();

        // Filter detail permintaan sesuai kategori
        $detailPermintaan = $permintaan->detailPermintaan->filter(function($detail) use ($kategoriGudang) {
            // Cek apakah barang ini termasuk dalam kategori yang diminta
            $inventory = DataInventory::where('id_data_barang', $detail->id_data_barang)
                ->where('jenis_inventory', $kategoriGudang)
                ->first();
            return $inventory !== null;
        });

        $satuans = MasterSatuan::all();

        return view('transaction.draft-distribusi.create', compact(
            'approvalLog',
            'permintaan',
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
        $isAdmin = $user->hasRole('admin') || $user->hasRole('admin_gudang');
        $isViewOnly = $user->hasRole('kepala_unit') || $user->hasRole('kepala_pusat') || $user->hasRole('kasubbag_tu');
        
        // View-only role tidak bisa menyimpan disposisi
        if ($isViewOnly) {
            abort(403, 'Anda hanya dapat melihat disposisi, tidak dapat menyimpan.');
        }
        
        if ($isAdmin) {
            // Admin harus menyertakan kategori dalam request
            $kategoriGudang = $request->input('kategori_gudang');
            // Jika tidak ada di request, coba ambil dari session atau default ke ASET
            if (!$kategoriGudang || !in_array($kategoriGudang, ['ASET', 'PERSEDIAAN', 'FARMASI'])) {
                // Coba ambil dari approval log jika ada
                $idPermintaan = $request->input('id_permintaan');
                if ($idPermintaan) {
                    // Cek detail permintaan untuk menentukan kategori
                    $permintaan = PermintaanBarang::with('detailPermintaan.dataBarang')->find($idPermintaan);
                    if ($permintaan && $permintaan->detailPermintaan->isNotEmpty()) {
                        // Ambil kategori dari inventory pertama yang ditemukan
                        $firstDetail = $permintaan->detailPermintaan->first();
                        $inventory = DataInventory::where('id_data_barang', $firstDetail->id_data_barang)
                            ->whereIn('jenis_inventory', ['ASET', 'PERSEDIAAN', 'FARMASI'])
                            ->first();
                        if ($inventory) {
                            $kategoriGudang = $inventory->jenis_inventory;
                        }
                    }
                }
                // Jika masih tidak ada, default ke ASET
                if (!$kategoriGudang || !in_array($kategoriGudang, ['ASET', 'PERSEDIAAN', 'FARMASI'])) {
                    $kategoriGudang = 'ASET';
                }
            }
        } elseif ($user->hasRole('admin_gudang_aset')) {
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
            'kategori_gudang' => 'nullable|in:ASET,PERSEDIAAN,FARMASI',
            'detail' => 'required|array|min:1',
            'detail.*.id_inventory' => 'required|exists:data_inventory,id_inventory',
            'detail.*.id_gudang_asal' => 'required|exists:master_gudang,id_gudang',
            'detail.*.qty_distribusi' => 'required|numeric|min:0.01',
            'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
            'detail.*.harga_satuan' => 'required|numeric|min:0',
            'detail.*.keterangan' => 'nullable|string',
        ], [
            'id_permintaan.required' => 'ID Permintaan wajib diisi.',
            'id_permintaan.exists' => 'Permintaan tidak ditemukan.',
            'detail.required' => 'Detail distribusi wajib diisi.',
            'detail.array' => 'Detail distribusi harus berupa array.',
            'detail.min' => 'Minimal harus ada 1 item distribusi.',
            'detail.*.id_inventory.required' => 'Inventory wajib dipilih untuk setiap item.',
            'detail.*.id_inventory.exists' => 'Inventory yang dipilih tidak valid.',
            'detail.*.id_gudang_asal.required' => 'Gudang asal wajib dipilih untuk setiap item.',
            'detail.*.id_gudang_asal.exists' => 'Gudang asal yang dipilih tidak valid.',
            'detail.*.qty_distribusi.required' => 'Qty distribusi wajib diisi untuk setiap item.',
            'detail.*.qty_distribusi.numeric' => 'Qty distribusi harus berupa angka.',
            'detail.*.qty_distribusi.min' => 'Qty distribusi minimal 0.01.',
            'detail.*.id_satuan.required' => 'Satuan wajib dipilih untuk setiap item.',
            'detail.*.id_satuan.exists' => 'Satuan yang dipilih tidak valid.',
            'detail.*.harga_satuan.required' => 'Harga satuan wajib diisi untuk setiap item.',
            'detail.*.harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
            'detail.*.harga_satuan.min' => 'Harga satuan minimal 0.',
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
            $approvalLogQuery = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('id_referensi', $validated['id_permintaan'])
                ->where('status', 'MENUNGGU');
            
            if ($isAdmin) {
                // Admin perlu mencari role_id berdasarkan kategori
                $roleMap = [
                    'ASET' => 'admin_gudang_aset',
                    'PERSEDIAAN' => 'admin_gudang_persediaan',
                    'FARMASI' => 'admin_gudang_farmasi',
                ];
                if (isset($roleMap[$kategoriGudang])) {
                    $roleId = \App\Models\Role::where('name', $roleMap[$kategoriGudang])->first()?->id;
                    if ($roleId) {
                        $approvalLogQuery->where('role_id', $roleId);
                    }
                }
            } else {
                $roleId = $user->roles()->where('name', 'like', 'admin_gudang_%')->first()?->id;
                if ($roleId) {
                    $approvalLogQuery->where('role_id', $roleId);
                }
            }
            
            $approvalLog = $approvalLogQuery->first();

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error creating draft distribusi: ' . json_encode($e->errors()));
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating draft distribusi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail draft distribusi berdasarkan approval log
     */
    public function show(Request $request, $approvalLogId)
    {
        $user = Auth::user();
        
        // Tentukan kategori gudang berdasarkan role user
        $kategoriGudang = null;
        $isAdmin = $user->hasRole('admin') || $user->hasRole('admin_gudang');
        $isViewOnly = $user->hasRole('kepala_unit') || $user->hasRole('kepala_pusat') || $user->hasRole('kasubbag_tu');
        
        if ($isAdmin) {
            // Admin bisa melihat semua kategori - ambil dari filter request jika ada
            $kategoriGudang = $request->get('kategori', null);
        } elseif ($user->hasRole('admin_gudang_aset')) {
            $kategoriGudang = 'ASET';
        } elseif ($user->hasRole('admin_gudang_persediaan')) {
            $kategoriGudang = 'PERSEDIAAN';
        } elseif ($user->hasRole('admin_gudang_farmasi')) {
            $kategoriGudang = 'FARMASI';
        } elseif ($isViewOnly) {
            // Kepala unit, kepala pusat, dan kasubbag bisa melihat semua untuk monitoring
            $kategoriGudang = $request->get('kategori', null);
        } else {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail disposisi.');
        }

        $approvalLog = ApprovalLog::findOrFail($approvalLogId);
        
        // Validasi modul approval
        if ($approvalLog->modul_approval !== 'PERMINTAAN_BARANG') {
            abort(404, 'Approval log ini bukan untuk permintaan barang.');
        }
        
        // Load permintaan secara manual karena relationship conditional
        $permintaan = PermintaanBarang::with(['unitKerja', 'pemohon'])
            ->find($approvalLog->id_referensi);
        
        if (!$permintaan) {
            abort(404, 'Permintaan barang tidak ditemukan.');
        }

        // Ambil semua draft detail untuk permintaan ini dengan kategori yang sesuai
        $draftDetailsQuery = DraftDetailDistribusi::where('id_permintaan', $permintaan->id_permintaan);
        
        if ($kategoriGudang) {
            $draftDetailsQuery->where('kategori_gudang', $kategoriGudang);
        }
        
        // Admin bisa melihat semua, user biasa hanya miliknya sendiri
        if (!$isAdmin) {
            $draftDetailsQuery->where('created_by', $user->id);
        }
        
        $draftDetails = $draftDetailsQuery->with([
                'inventory.dataBarang',
                'gudangAsal',
                'satuan',
                'createdBy'
            ])
            ->get();

        return view('transaction.draft-distribusi.show', compact('approvalLog', 'permintaan', 'draftDetails', 'kategoriGudang'));
    }
}

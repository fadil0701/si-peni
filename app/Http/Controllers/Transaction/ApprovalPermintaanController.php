<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ApprovalFlowDefinition;
use App\Models\ApprovalLog;
use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use App\Models\MasterPegawai;
use App\Models\Role;
use App\Models\DataInventory;
use App\Models\DataStock;

class ApprovalPermintaanController extends Controller
{
    /**
     * Menampilkan daftar approval yang perlu diproses oleh user saat ini
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Pastikan roles ter-load
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }
        
        $userRoles = $user->roles->pluck('id')->toArray();
        
        // Ambil flow definition yang sesuai dengan role user saat ini
        $flowDefinitions = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
            ->whereIn('role_id', $userRoles)
            ->pluck('id');
        
        // Ambil approval log yang menunggu persetujuan
        // Jika user adalah admin, tampilkan semua approval log
        // Jika tidak, tampilkan hanya yang sesuai dengan role user
        if ($user->hasRole('admin')) {
            $query = ApprovalLog::with(['approvalFlow.role', 'user', 'permintaan'])
                ->where('modul_approval', 'PERMINTAAN_BARANG')
                ->whereIn('status', ['MENUNGGU', 'DIKETAHUI', 'DIVERIFIKASI', 'DIDISPOSISIKAN']);
        } else {
            // Ambil approval log yang menunggu persetujuan untuk role user saat ini
            // Gunakan whereIn untuk id_approval_flow yang sesuai dengan role user
            if ($flowDefinitions->isEmpty()) {
                // Jika tidak ada flow definition yang sesuai, tidak tampilkan apa-apa
                $query = ApprovalLog::with(['approvalFlow.role', 'user', 'permintaan'])
                    ->where('modul_approval', 'PERMINTAAN_BARANG')
                    ->whereRaw('1 = 0'); // Tidak tampilkan apa-apa
            } else {
                $query = ApprovalLog::with(['approvalFlow.role', 'user', 'permintaan'])
                    ->where('modul_approval', 'PERMINTAAN_BARANG')
                    ->whereIn('id_approval_flow', $flowDefinitions)
                    ->whereIn('status', ['MENUNGGU', 'DIKETAHUI', 'DIVERIFIKASI', 'DIDISPOSISIKAN']);
            }
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter hanya yang menunggu
        if ($request->filled('menunggu')) {
            $query->where('status', 'MENUNGGU');
        }
        
        // Filter berdasarkan tanggal mulai (berdasarkan tanggal permintaan)
        if ($request->filled('tanggal_mulai')) {
            $query->whereHas('permintaan', function($q) use ($request) {
                $q->whereDate('tanggal_permintaan', '>=', $request->tanggal_mulai);
            });
        }
        
        // Filter berdasarkan tanggal akhir (berdasarkan tanggal permintaan)
        if ($request->filled('tanggal_akhir')) {
            $query->whereHas('permintaan', function($q) use ($request) {
                $q->whereDate('tanggal_permintaan', '<=', $request->tanggal_akhir);
            });
        }
        
        // Ambil semua approval log untuk menentukan status per permintaan
        $allApprovals = $query->with(['approvalFlow' => function($q) {
            $q->with('role');
        }, 'permintaan'])->get();
        
        // Kelompokkan berdasarkan id_referensi (permintaan)
        $permintaanGroups = [];
        foreach ($allApprovals as $approval) {
            $idReferensi = $approval->id_referensi;
            if (!isset($permintaanGroups[$idReferensi])) {
                $permintaanGroups[$idReferensi] = [
                    'permintaan_id' => $idReferensi,
                    'approvals' => [],
                    'current_step' => null,
                    'current_status' => null,
                    'latest_approval' => null,
                ];
            }
            $permintaanGroups[$idReferensi]['approvals'][] = $approval;
            
            // Tentukan approval terakhir berdasarkan created_at
            if (!$permintaanGroups[$idReferensi]['latest_approval'] || 
                $approval->created_at > $permintaanGroups[$idReferensi]['latest_approval']->created_at) {
                $permintaanGroups[$idReferensi]['latest_approval'] = $approval;
            }
        }
        
        // Tentukan status dan step untuk setiap permintaan berdasarkan progress approval
        foreach ($permintaanGroups as $idReferensi => &$group) {
            // Urutkan approvals berdasarkan step_order
            usort($group['approvals'], function($a, $b) {
                $stepA = $a->approvalFlow->step_order ?? 999;
                $stepB = $b->approvalFlow->step_order ?? 999;
                return $stepA <=> $stepB;
            });
            
            // Tentukan step terakhir yang sudah diselesaikan
            $lastCompletedStep = null;
            $currentStep = null;
            $currentStatus = 'MENUNGGU';
            $maxCompletedStep = 0;
            $rejectedApproval = null;
            
            // PRIORITAS 1: Cek apakah ada yang ditolak - jika ada, status harus DITOLAK
            foreach ($group['approvals'] as $approval) {
                if ($approval->status === 'DITOLAK') {
                    $rejectedApproval = $approval;
                    $currentStatus = 'DITOLAK';
                    $currentStep = $approval;
                    break; // Setelah ditemukan DITOLAK, langsung keluar
                }
            }
            
            // Jika tidak ada yang ditolak, lanjutkan pengecekan normal
            if (!$rejectedApproval) {
                // Urutkan approvals berdasarkan step_order untuk memastikan urutan yang benar
                usort($group['approvals'], function($a, $b) {
                    $stepA = $a->approvalFlow->step_order ?? 999;
                    $stepB = $b->approvalFlow->step_order ?? 999;
                    return $stepA <=> $stepB;
                });
                
                foreach ($group['approvals'] as $approval) {
                    $stepOrder = $approval->approvalFlow->step_order ?? 999;
                    
                    // Jika status sudah diselesaikan (bukan MENUNGGU), update last completed step
                    if (in_array($approval->status, ['DIKETAHUI', 'DIVERIFIKASI', 'DISETUJUI', 'DIDISPOSISIKAN', 'DIPROSES'])) {
                        if ($stepOrder > $maxCompletedStep) {
                            $maxCompletedStep = $stepOrder;
                            $lastCompletedStep = $stepOrder;
                        }
                    }
                }
                
                // Cari current step berdasarkan urutan step_order (prioritas step yang lebih tinggi)
                // Step 4 (disposisi) harus ditampilkan sebagai DIDISPOSISIKAN meskipun status approval lognya MENUNGGU
                
                // Cek apakah step 3 sudah diverifikasi
                $step3Verified = false;
                $step3Approval = null;
                foreach ($group['approvals'] as $approval) {
                    $stepOrder = $approval->approvalFlow->step_order ?? 999;
                    if ($stepOrder == 3 && $approval->status === 'DIVERIFIKASI') {
                        $step3Verified = true;
                        $step3Approval = $approval;
                        break;
                    }
                }
                
                // Cek apakah ada step 4 (disposisi)
                $step4Approval = null;
                foreach ($group['approvals'] as $approval) {
                    $stepOrder = $approval->approvalFlow->step_order ?? 999;
                    if ($stepOrder == 4) {
                        $step4Approval = $approval;
                        break;
                    }
                }
                
                // Prioritas 1: Cek step 4 (disposisi) dulu - ini adalah step terpenting untuk ditampilkan
                if ($step4Approval) {
                    if ($step4Approval->status === 'MENUNGGU') {
                        $currentStep = $step4Approval;
                        // Jika step 3 sudah diverifikasi, status = DISETUJUI (karena sudah diverifikasi dan didisposisikan)
                        // Jika step 3 belum diverifikasi, status = DIDISPOSISIKAN
                        $currentStatus = $step3Verified ? 'DISETUJUI' : 'DIDISPOSISIKAN';
                    } elseif ($step4Approval->status === 'DIPROSES') {
                        $currentStep = $step4Approval;
                        $currentStatus = 'DIPROSES';
                    }
                }
                
                // Prioritas 2: Jika belum ada step 4, cek step 3 (verifikasi Kasubbag TU)
                if (!$currentStep) {
                    foreach ($group['approvals'] as $approval) {
                        $stepOrder = $approval->approvalFlow->step_order ?? 999;
                        if ($stepOrder == 3) {
                            if ($approval->status === 'MENUNGGU') {
                                $currentStep = $approval;
                                $currentStatus = 'MENUNGGU'; // Masih menunggu verifikasi
                            } elseif ($approval->status === 'DIVERIFIKASI') {
                                // Step 3 sudah diverifikasi -> status DISETUJUI
                                $currentStep = $approval;
                                $currentStatus = 'DISETUJUI'; // Status ditampilkan sebagai DISETUJUI karena sudah diverifikasi
                            }
                            break;
                        }
                    }
                }
                
                // Prioritas 3: Jika belum ada step 3, cek step 2 (mengetahui Kepala Unit)
                if (!$currentStep) {
                    foreach ($group['approvals'] as $approval) {
                        $stepOrder = $approval->approvalFlow->step_order ?? 999;
                        if ($stepOrder == 2 && $approval->status === 'MENUNGGU') {
                            $currentStep = $approval;
                            $currentStatus = 'MENUNGGU'; // Masih menunggu diketahui
                            break;
                        }
                    }
                }
                
                // Jika masih belum ada yang menunggu, gunakan approval terakhir
                if (!$currentStep) {
                    $currentStep = $group['latest_approval'];
                    if ($currentStep) {
                        // Jika approval terakhir adalah DIVERIFIKASI dan sudah ada step 4, status = DISETUJUI
                        if ($currentStep->status === 'DIVERIFIKASI' && $step4Approval) {
                            $currentStatus = 'DISETUJUI';
                            $currentStep = $step4Approval;
                        } else {
                            $currentStatus = $currentStep->status;
                        }
                    }
                }
            } else {
                // Jika ada yang ditolak, tetap hitung last completed step untuk display
                foreach ($group['approvals'] as $approval) {
                    $stepOrder = $approval->approvalFlow->step_order ?? 999;
                    if (in_array($approval->status, ['DIKETAHUI', 'DIVERIFIKASI', 'DISETUJUI', 'DIDISPOSISIKAN', 'DIPROSES'])) {
                        if ($stepOrder > $maxCompletedStep) {
                            $maxCompletedStep = $stepOrder;
                            $lastCompletedStep = $stepOrder;
                        }
                    }
                }
            }
            
            $group['current_step'] = $currentStep;
            $group['current_status'] = $currentStatus;
            $group['last_completed_step'] = $lastCompletedStep;
        }
        
        // Ambil data permintaan untuk setiap group
        $permintaanIds = array_keys($permintaanGroups);
        $permintaans = PermintaanBarang::with([
            'unitKerja.gudang', // Load gudang unit melalui unit kerja
            'pemohon.jabatan', // Load jabatan pemohon
            'detailPermintaan.dataBarang'
        ])
            ->whereIn('id_permintaan', $permintaanIds)
            ->get()
            ->keyBy('id_permintaan');
        
        // Convert ke collection untuk pagination
        $permintaanList = collect($permintaanGroups)->map(function($group) use ($permintaans) {
            return [
                'permintaan' => $permintaans[$group['permintaan_id']] ?? null,
                'current_step' => $group['current_step'],
                'current_status' => $group['current_status'],
                'last_completed_step' => $group['last_completed_step'],
                'approvals' => $group['approvals'],
            ];
        })->filter(function($item) {
            return $item['permintaan'] !== null;
        });
        
        // Pagination manual
        $page = $request->get('page', 1);
        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $total = $permintaanList->count();
        $items = $permintaanList->slice(($page - 1) * $perPage, $perPage)->values();
        
        // Buat paginator manual
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('transaction.approval.index', compact('paginator', 'permintaans'));
    }

    /**
     * Menampilkan detail approval
     */
    public function show($id)
    {
        $approval = ApprovalLog::with(['approvalFlow.role', 'user', 'role'])
            ->findOrFail($id);
        
        // Pastikan user yang login memiliki hak akses untuk approval ini
        $user = Auth::user();
        
        // Pastikan roles ter-load
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }
        
        $userRoles = $user->roles->pluck('id')->toArray();
        
        // Admin bisa melihat semua approval
        if (!$user->hasRole('admin')) {
            $allowedFlowIds = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                ->whereIn('role_id', $userRoles)
                ->pluck('id')
                ->toArray();
            
            if (!in_array($approval->id_approval_flow, $allowedFlowIds)) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat approval ini.');
            }
        }
        
        // Load permintaan
        $permintaan = PermintaanBarang::with([
            'unitKerja', 
            'pemohon.jabatan', 
            'detailPermintaan.dataBarang', 
            'detailPermintaan.satuan'
        ])->find($approval->id_referensi);
        
        if (!$permintaan) {
            abort(404, 'Permintaan tidak ditemukan.');
        }
        
        // Get stock data hanya gudang pusat (untuk detail yang dari master). Permintaan lainnya tidak punya stock.
        $stockData = [];
        foreach ($permintaan->detailPermintaan as $detail) {
            if ($detail->id_data_barang) {
                $perGudangPusat = DataStock::getStockPerGudangPusat($detail->id_data_barang);
                $stockData[$detail->id_detail_permintaan] = [
                    'total' => $perGudangPusat->sum('qty_akhir'),
                    'per_gudang' => $perGudangPusat,
                ];
            } else {
                $stockData[$detail->id_detail_permintaan] = ['total' => 0, 'per_gudang' => collect()];
            }
        }
        
        // Load approval history
        $approvalHistory = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('id_referensi', $approval->id_referensi)
            ->with(['approvalFlow.role', 'user', 'role'])
            ->orderBy('created_at')
            ->get();
        
        // Cek apakah ada approval yang ditolak untuk permintaan ini
        $rejectedApproval = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('id_referensi', $approval->id_referensi)
            ->where('status', 'DITOLAK')
            ->first();
        
        // Jika ada yang ditolak, gunakan status DITOLAK untuk display
        $displayStatus = $rejectedApproval ? 'DITOLAK' : $approval->status;
        
        // Load current flow definition
        $currentFlow = $approval->approvalFlow;
        $nextFlow = $currentFlow ? $currentFlow->getNextStep() : null;
        
        // Cek apakah step 3 (Kasubbag TU) sudah diverifikasi untuk menentukan apakah bisa disposisi
        $step3Verified = false;
        $step3Flow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('step_order', 3)
            ->first();
        if ($step3Flow) {
            $step3Log = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('id_referensi', $approval->id_referensi)
                ->where('id_approval_flow', $step3Flow->id)
                ->first();
            $step3Verified = $step3Log && $step3Log->status === 'DIVERIFIKASI';
        }
        
        return view('transaction.approval.show', compact('approval', 'permintaan', 'approvalHistory', 'currentFlow', 'nextFlow', 'step3Verified', 'displayStatus', 'rejectedApproval', 'stockData'));
    }

    /**
     * Kepala Unit - Mengetahui permintaan
     */
    public function mengetahui(Request $request, $id)
    {
        $approval = ApprovalLog::with('approvalFlow')->findOrFail($id);
        $user = Auth::user();
        
        // Validasi role
        if (!$user->hasRole('kepala_unit') && !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak untuk mengetahui permintaan ini.');
        }
        
        // Validasi status
        if ($approval->status !== 'MENUNGGU') {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Approval ini sudah diproses.');
        }
        
        // Validasi flow step
        if ($approval->approvalFlow->step_order !== 2) {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Step approval tidak sesuai.');
        }
        
        DB::beginTransaction();
        try {
            // Update approval log
            $approval->update([
                'status' => 'DIKETAHUI',
                'catatan' => $request->catatan ?? null,
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);
            
            // Buat approval log untuk step berikutnya (Kasubbag TU)
            $nextFlow = $approval->approvalFlow->getNextStep();
            if ($nextFlow) {
                ApprovalLog::create([
                    'modul_approval' => $approval->modul_approval,
                    'id_referensi' => $approval->id_referensi,
                    'id_approval_flow' => $nextFlow->id,
                    'user_id' => null, // Akan diisi saat di-approve
                    'role_id' => $nextFlow->role_id,
                    'status' => 'MENUNGGU',
                    'catatan' => null,
                    'approved_at' => null,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('transaction.approval.show', $id)
                ->with('success', 'Permintaan telah diketahui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error mengetahui approval: ' . $e->getMessage());
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Kasubbag TU - Verifikasi permintaan
     */
    public function verifikasi(Request $request, $id)
    {
        $approval = ApprovalLog::with('approvalFlow')->findOrFail($id);
        $user = Auth::user();
        
        // Validasi role
        if (!$user->hasRole('kasubbag_tu') && !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak untuk memverifikasi permintaan ini.');
        }
        
        // Validasi status
        if ($approval->status !== 'MENUNGGU') {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Approval ini sudah diproses.');
        }
        
        // Validasi flow step
        if ($approval->approvalFlow->step_order !== 3) {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Step approval tidak sesuai.');
        }
        
        $validated = $request->validate([
            'catatan' => 'nullable|string',
            'koreksi_qty' => 'nullable|array',
            'koreksi_qty.*' => 'nullable|numeric|min:0.01',
        ]);
        
        // Load permintaan untuk validasi stock
        $permintaan = PermintaanBarang::with('detailPermintaan')->find($approval->id_referensi);
        
        // Validasi koreksi qty jika ada (hanya untuk barang dari master yang punya stock)
        // Barang tanpa stock (permintaan lainnya / id_data_barang null) atau stock 0 tetap dapat didisposisikan
        if (isset($validated['koreksi_qty']) && is_array($validated['koreksi_qty'])) {
            foreach ($validated['koreksi_qty'] as $detailId => $qtyBaru) {
                if ($qtyBaru !== null) {
                    $detail = $permintaan->detailPermintaan->find($detailId);
                    if ($detail && $detail->id_data_barang) {
                        $totalStock = DataStock::getTotalStock($detail->id_data_barang);
                        // Hanya blokir jika ada stock dan koreksi melebihi stock; jika stock 0 tetap boleh didisposisikan
                        if ($totalStock > 0 && $qtyBaru > $totalStock) {
                            return back()->withInput()->withErrors([
                                "koreksi_qty.{$detailId}" => "Jumlah yang dikoreksi ({$qtyBaru}) melebihi stock tersedia ({$totalStock})."
                            ]);
                        }
                    }
                }
            }
        }
        
        DB::beginTransaction();
        try {
            // Update qty jika ada koreksi
            if (isset($validated['koreksi_qty']) && is_array($validated['koreksi_qty'])) {
                foreach ($validated['koreksi_qty'] as $detailId => $qtyBaru) {
                    if ($qtyBaru !== null) {
                        $detail = DetailPermintaanBarang::find($detailId);
                        if ($detail && $detail->id_permintaan == $permintaan->id_permintaan) {
                            $detail->update(['qty_diminta' => $qtyBaru]);
                        }
                    }
                }
            }
            
            // Update approval log
            $approval->update([
                'status' => 'DIVERIFIKASI',
                'catatan' => $validated['catatan'] ?? null,
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);
            
            // Update status permintaan menjadi DISETUJUI (setelah verifikasi Kasubbag TU, langsung bisa disposisi)
            $permintaan = PermintaanBarang::find($approval->id_referensi);
            if ($permintaan) {
                $permintaan->update(['status_permintaan' => 'DISETUJUI']);
            }
            
            // Langsung membuat approval log untuk disposisi (step 4) ke admin gudang sesuai kategori
            // Ambil jenis permintaan dari permintaan
            $jenisPermintaan = is_array($permintaan->jenis_permintaan) 
                ? $permintaan->jenis_permintaan 
                : (is_string($permintaan->jenis_permintaan) ? json_decode($permintaan->jenis_permintaan, true) : []);
            
            // Permintaan rutin/cito hanya Persediaan & Farmasi (Aset tidak masuk alur permintaan barang)
            $kategoriGudang = array_values(array_intersect($jenisPermintaan, ['PERSEDIAAN', 'FARMASI']));
            
            // Jika tidak ada kategori spesifik, gunakan admin_gudang umum
            if (empty($kategoriGudang)) {
                $kategoriGudang = ['UMUM'];
            }
            
            // Buat approval log untuk setiap kategori gudang yang perlu didisposisikan
            $roleMap = [
                'ASET' => 'admin_gudang_aset',
                'PERSEDIAAN' => 'admin_gudang_persediaan',
                'FARMASI' => 'admin_gudang_farmasi',
                'UMUM' => 'admin_gudang',
            ];
            
            foreach ($kategoriGudang as $kategori) {
                $roleName = $roleMap[$kategori] ?? 'admin_gudang';
                $role = \App\Models\Role::where('name', $roleName)->first();
                
                if ($role) {
                    // Cari flow definition untuk step 4 dengan role ini
                    $step4Flow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                        ->where('step_order', 4)
                        ->where('role_id', $role->id)
                        ->first();
                    
                    if ($step4Flow) {
                        // Cek apakah sudah ada approval log untuk step 4 dengan role ini
                        $existingLog = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                            ->where('id_referensi', $permintaan->id_permintaan)
                            ->where('id_approval_flow', $step4Flow->id)
                            ->first();
                        
                        if (!$existingLog) {
                            // Buat approval log baru untuk disposisi dengan status MENUNGGU
                            ApprovalLog::create([
                                'modul_approval' => 'PERMINTAAN_BARANG',
                                'id_referensi' => $permintaan->id_permintaan,
                                'id_approval_flow' => $step4Flow->id,
                                'user_id' => null, // Akan diisi saat admin gudang memproses
                                'role_id' => $role->id,
                                'status' => 'MENUNGGU',
                                'catatan' => 'Didisposisikan oleh Kasubbag TU setelah verifikasi',
                                'approved_at' => null,
                            ]);
                        } else {
                            // Update existing log jika status bukan MENUNGGU
                            if ($existingLog->status !== 'MENUNGGU') {
                                $existingLog->update([
                                    'status' => 'MENUNGGU',
                                    'user_id' => null,
                                    'approved_at' => null,
                                ]);
                            }
                        }
                    }
                }
            }
            
            // Jika ada item yang tidak ada di stock (permintaan lainnya atau stock gudang pusat = 0), disposisi ke Pengadaan Barang dan Jasa
            $permintaan->load('detailPermintaan');
            $adaItemTanpaStock = false;
            foreach ($permintaan->detailPermintaan as $detail) {
                if (!$detail->id_data_barang) {
                    $adaItemTanpaStock = true; // permintaan lainnya / freetext
                    break;
                }
                $stockPusat = DataStock::getStockPerGudangPusat($detail->id_data_barang);
                $totalStockPusat = $stockPusat->sum('qty_akhir');
                if ($totalStockPusat <= 0) {
                    $adaItemTanpaStock = true;
                    break;
                }
            }
            if ($adaItemTanpaStock) {
                $rolePengadaan = \App\Models\Role::where('name', 'pengadaan')->first();
                if ($rolePengadaan) {
                    $step4PengadaanFlow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                        ->where('step_order', 4)
                        ->where('role_id', $rolePengadaan->id)
                        ->first();
                    if ($step4PengadaanFlow) {
                        $existingLogPengadaan = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                            ->where('id_referensi', $permintaan->id_permintaan)
                            ->where('id_approval_flow', $step4PengadaanFlow->id)
                            ->first();
                        if (!$existingLogPengadaan) {
                            ApprovalLog::create([
                                'modul_approval' => 'PERMINTAAN_BARANG',
                                'id_referensi' => $permintaan->id_permintaan,
                                'id_approval_flow' => $step4PengadaanFlow->id,
                                'user_id' => null,
                                'role_id' => $rolePengadaan->id,
                                'status' => 'MENUNGGU',
                                'catatan' => 'Didisposisikan ke Pengadaan Barang dan Jasa: terdapat item yang tidak ada di stock gudang pusat, untuk dilakukan pengadaan.',
                                'approved_at' => null,
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('transaction.approval.show', $id)
                ->with('success', 'Permintaan telah diverifikasi, disetujui, dan didisposisikan ke Admin Gudang/Pengurus Barang.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error verifikasi approval: ' . $e->getMessage());
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Kasubbag TU - Kembalikan permintaan
     */
    public function kembalikan(Request $request, $id)
    {
        $approval = ApprovalLog::with('approvalFlow')->findOrFail($id);
        $user = Auth::user();
        
        // Validasi role
        if (!$user->hasRole('kasubbag_tu') && !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak untuk mengembalikan permintaan ini.');
        }
        
        // Validasi status
        if ($approval->status !== 'MENUNGGU') {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Approval ini sudah diproses.');
        }
        
        $validated = $request->validate([
            'catatan' => 'required|string|min:10',
        ], [
            'catatan.required' => 'Catatan pengembalian wajib diisi.',
            'catatan.min' => 'Catatan pengembalian minimal 10 karakter.',
        ]);
        
        DB::beginTransaction();
        try {
            // Update approval log menjadi ditolak
            $approval->update([
                'status' => 'DITOLAK',
                'catatan' => $validated['catatan'],
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);
            
            // Update status permintaan menjadi DITOLAK
            $permintaan = PermintaanBarang::find($approval->id_referensi);
            if ($permintaan) {
                $permintaan->update(['status_permintaan' => 'DITOLAK']);
            }
            
            DB::commit();
            
            return redirect()->route('transaction.approval.index')
                ->with('success', 'Permintaan telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error mengembalikan approval: ' . $e->getMessage());
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Kepala Pusat - Approve permintaan
     */
    public function approve(Request $request, $id)
    {
        Log::info('Approval approve called', [
            'approval_id' => $id,
            'user_id' => Auth::id(),
            'user_roles' => Auth::user()->roles->pluck('name')->toArray()
        ]);
        
        $approval = ApprovalLog::with(['approvalFlow'])->findOrFail($id);
        $user = Auth::user();
        
        Log::info('Approval data loaded', [
            'approval_id' => $approval->id,
            'status' => $approval->status,
            'step_order' => $approval->approvalFlow->step_order ?? null,
            'id_referensi' => $approval->id_referensi
        ]);
        
        // Pastikan approvalFlow ter-load
        if (!$approval->relationLoaded('approvalFlow') || !$approval->approvalFlow) {
            $approval->load('approvalFlow');
        }
        
        // Validasi role
        if (!$user->hasRole('kepala_pusat') && !$user->hasRole('admin')) {
            Log::warning('User tidak memiliki role untuk approve', [
                'user_id' => $user->id,
                'user_roles' => $user->roles->pluck('name')->toArray()
            ]);
            abort(403, 'Anda tidak memiliki hak untuk menyetujui permintaan ini.');
        }
        
        // Validasi status
        if ($approval->status !== 'MENUNGGU') {
            Log::warning('Approval sudah diproses', [
                'approval_id' => $approval->id,
                'status' => $approval->status
            ]);
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Approval ini sudah diproses.');
        }
        
        // Validasi flow step
        if ($approval->approvalFlow->step_order !== 4) {
            Log::warning('Step approval tidak sesuai', [
                'approval_id' => $approval->id,
                'step_order' => $approval->approvalFlow->step_order,
                'expected' => 4
            ]);
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Step approval tidak sesuai.');
        }
        
        // Validasi: Pastikan step sebelumnya (kasubbag_tu - step 3) sudah diverifikasi
        $previousStep = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('step_order', 3)
            ->first();
        
        $previousStepVerified = true;
        if ($previousStep) {
            $previousApprovalLog = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('id_referensi', $approval->id_referensi)
                ->where('id_approval_flow', $previousStep->id)
                ->first();
            
            if (!$previousApprovalLog || $previousApprovalLog->status !== 'DIVERIFIKASI') {
                $previousStepVerified = false;
                // Untuk admin, tetap bisa approve meski step sebelumnya belum diverifikasi
                // Tapi untuk kepala_pusat, harus menunggu step sebelumnya diverifikasi
                if (!$user->hasRole('admin')) {
                    return redirect()->route('transaction.approval.show', $id)
                        ->with('error', 'Permintaan harus diverifikasi oleh Kasubbag TU terlebih dahulu sebelum dapat disetujui.');
                }
            }
        }
        
        $validated = $request->validate([
            'catatan' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            // Update approval log
            $approval->update([
                'status' => 'DISETUJUI',
                'catatan' => $validated['catatan'] ?? null,
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);
            
            // Update status permintaan menjadi DISETUJUI_PIMPINAN (karena sudah disetujui oleh Kepala Pusat)
            $permintaan = PermintaanBarang::find($approval->id_referensi);
            if ($permintaan) {
                $permintaan->update(['status_permintaan' => 'DISETUJUI_PIMPINAN']);
            }
            
            // Catatan: Approval log untuk disposisi TIDAK dibuat di sini
            // Approval log untuk disposisi akan dibuat saat admin gudang melakukan disposisi
            // di method disposisi(), sehingga tidak terjadi duplikasi
            
            DB::commit();
            
            return redirect()->route('transaction.approval.show', $id)
                ->with('success', 'Permintaan berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approve approval', [
                'approval_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Terjadi kesalahan saat menyetujui permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Kepala Pusat - Reject permintaan
     */
    public function reject(Request $request, $id)
    {
        $approval = ApprovalLog::with('approvalFlow')->findOrFail($id);
        $user = Auth::user();
        
        // Validasi role
        if (!$user->hasRole('kepala_pusat') && !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak untuk menolak permintaan ini.');
        }
        
        // Validasi status
        if ($approval->status !== 'MENUNGGU') {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Approval ini sudah diproses.');
        }
        
        // Validasi flow step
        if ($approval->approvalFlow->step_order !== 4) {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Step approval tidak sesuai.');
        }
        
        // Validasi: Pastikan step sebelumnya (kasubbag_tu - step 3) sudah diverifikasi
        $previousStep = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('step_order', 3)
            ->first();
        
        if ($previousStep) {
            $previousApprovalLog = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('id_referensi', $approval->id_referensi)
                ->where('id_approval_flow', $previousStep->id)
                ->first();
            
            if (!$previousApprovalLog || $previousApprovalLog->status !== 'DIVERIFIKASI') {
                return redirect()->route('transaction.approval.show', $id)
                    ->with('error', 'Permintaan harus diverifikasi oleh Kasubbag TU terlebih dahulu sebelum dapat ditolak.');
            }
        }
        
        $validated = $request->validate([
            'catatan' => 'required|string|min:10',
        ], [
            'catatan.required' => 'Catatan penolakan wajib diisi.',
            'catatan.min' => 'Catatan penolakan minimal 10 karakter.',
        ]);
        
        DB::beginTransaction();
        try {
            // Update approval log
            $approval->update([
                'status' => 'DITOLAK',
                'catatan' => $validated['catatan'],
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);
            
            // Update status permintaan menjadi DITOLAK
            $permintaan = PermintaanBarang::find($approval->id_referensi);
            if ($permintaan) {
                $permintaan->update(['status_permintaan' => 'DITOLAK']);
            }
            
            DB::commit();
            
            return redirect()->route('transaction.approval.index')
                ->with('success', 'Permintaan ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error reject approval: ' . $e->getMessage());
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Admin Gudang/Pengurus Barang - Disposisi ke admin gudang kategori sesuai item permintaan
     */
    public function disposisi(Request $request, $id)
    {
        // $id bisa berupa id approval log atau id permintaan
        // Cek apakah ini id approval log atau id permintaan
        $approval = ApprovalLog::with('approvalFlow')->find($id);
        if (!$approval) {
            // Jika bukan approval log, coba sebagai id permintaan
            $permintaan = PermintaanBarang::find($id);
            if (!$permintaan) {
                abort(404, 'Approval atau permintaan tidak ditemukan.');
            }
            // Ambil approval log verifikasi (step 3)
            $step3Flow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('step_order', 3)
                ->first();
            if ($step3Flow) {
                $approval = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                    ->where('id_referensi', $permintaan->id_permintaan)
                    ->where('id_approval_flow', $step3Flow->id)
                    ->first();
            }
        }
        
        if (!$approval) {
            abort(404, 'Approval tidak ditemukan.');
        }
        
        $user = Auth::user();
        
        // Validasi permission - Admin atau user dengan permission disposisi
        if (!$user->hasRole('admin') && !\App\Helpers\PermissionHelper::canAccess($user, 'transaction.approval.disposisi')) {
            abort(403, 'Anda tidak memiliki hak untuk mendisposisikan permintaan ini.');
        }
        
        // Validasi bahwa permintaan sudah diverifikasi oleh Kasubbag TU
        $permintaan = PermintaanBarang::find($approval->id_referensi);
        if (!$permintaan || $permintaan->status_permintaan !== 'DISETUJUI') {
            return redirect()->route('transaction.approval.show', $approval->id)
                ->with('error', 'Permintaan harus diverifikasi oleh Kasubbag TU terlebih dahulu sebelum didisposisikan.');
        }
        
        // Validasi bahwa step 3 (Kasubbag TU) sudah diverifikasi
        $step3Flow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
            ->where('step_order', 3)
            ->first();
        
        if ($step3Flow) {
            $step3Log = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('id_referensi', $approval->id_referensi)
                ->where('id_approval_flow', $step3Flow->id)
                ->first();
            
            if (!$step3Log || $step3Log->status !== 'DIVERIFIKASI') {
                return redirect()->route('transaction.approval.show', $approval->id)
                    ->with('error', 'Permintaan harus diverifikasi oleh Kasubbag TU terlebih dahulu sebelum didisposisikan.');
            }
        }

        DB::beginTransaction();
        try {
            // Buat approval log baru untuk disposisi (tidak update approval log verifikasi)
            // Approval log verifikasi tetap dengan status DIVERIFIKASI
            
            // Hapus approval log untuk admin_gudang yang sudah ada (jika ada)
            // Karena nanti akan dibuat approval log untuk setiap kategori gudang
            $adminGudangRole = Role::where('name', 'admin_gudang')->first();
            if ($adminGudangRole) {
                $adminGudangFlow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                    ->where('step_order', 5)
                    ->where('role_id', $adminGudangRole->id)
                    ->first();
                
                if ($adminGudangFlow) {
                    ApprovalLog::where('modul_approval', $approval->modul_approval)
                        ->where('id_referensi', $approval->id_referensi)
                        ->where('id_approval_flow', $adminGudangFlow->id)
                        ->where('status', 'MENUNGGU')
                        ->delete();
                }
            }
            
            // Tentukan kategori gudang yang terlibat: permintaan rutin/cito hanya Persediaan & Farmasi
            $jenisPermintaan = is_array($permintaan->jenis_permintaan) 
                ? $permintaan->jenis_permintaan 
                : json_decode($permintaan->jenis_permintaan, true) ?? [];
            $kategoriGudang = array_values(array_unique(array_intersect($jenisPermintaan, ['PERSEDIAAN', 'FARMASI'])));
            
            // Buat approval log untuk setiap kategori gudang yang terlibat
            foreach ($kategoriGudang as $kategori) {
                // Tentukan role_id berdasarkan kategori
                $roleName = match($kategori) {
                    'ASET' => 'admin_gudang_aset',
                    'PERSEDIAAN' => 'admin_gudang_persediaan',
                    'FARMASI' => 'admin_gudang_farmasi',
                    default => 'admin_gudang', // Fallback
                };
                
                $role = Role::where('name', $roleName)->first();
                
                \Log::info('Disposisi - Processing kategori', [
                    'kategori' => $kategori,
                    'role_name' => $roleName,
                    'role_found' => $role ? true : false,
                    'role_id' => $role ? $role->id : null
                ]);
                
                if ($role) {
                    // Cari atau buat flow definition untuk step disposisi dengan role ini (step_order = 4)
                    $disposisiFlow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                        ->where('step_order', 4)
                        ->where('role_id', $role->id)
                        ->first();
                    
                    // Jika belum ada flow definition untuk role ini, buat baru
                    if (!$disposisiFlow) {
                        $disposisiFlow = ApprovalFlowDefinition::create([
                            'modul_approval' => 'PERMINTAAN_BARANG',
                            'step_order' => 4,
                            'role_id' => $role->id,
                            'nama_step' => 'Didisposisikan - ' . $kategori,
                            'status' => 'MENUNGGU',
                            'status_text' => 'Permintaan telah didisposisikan ke Admin Gudang ' . $kategori,
                            'is_required' => false,
                            'can_reject' => false,
                            'can_approve' => false,
                        ]);
                    }
                    
                    // Pastikan tidak ada approval log yang sudah ada untuk step ini dengan role ini
                    $existingLog = ApprovalLog::where('modul_approval', $approval->modul_approval)
                        ->where('id_referensi', $approval->id_referensi)
                        ->where(function($q) use ($disposisiFlow, $role) {
                            $q->where('id_approval_flow', $disposisiFlow->id)
                              ->orWhere(function($q2) use ($role) {
                                  $q2->where('role_id', $role->id)
                                     ->whereHas('approvalFlow', function($q3) {
                                         $q3->where('step_order', 4)
                                            ->where('modul_approval', 'PERMINTAAN_BARANG');
                                     });
                              });
                        })
                        ->first();
                    
                    // Jika ada existing log dengan status DIDISPOSISIKAN, update menjadi MENUNGGU
                    if ($existingLog) {
                        if ($existingLog->status === 'DIDISPOSISIKAN' || $existingLog->id_approval_flow !== $disposisiFlow->id) {
                            $existingLog->update([
                                'id_approval_flow' => $disposisiFlow->id,
                                'status' => 'MENUNGGU',
                                'user_id' => null,
                                'approved_at' => null,
                                'role_id' => $role->id
                            ]);
                            \Log::info('Updated existing approval log', [
                                'approval_log_id' => $existingLog->id,
                                'id_referensi' => $approval->id_referensi,
                                'old_status' => 'DIDISPOSISIKAN',
                                'new_status' => 'MENUNGGU'
                            ]);
                        } else {
                            \Log::info('Approval log already exists and is correct', [
                                'existing_log_id' => $existingLog->id,
                                'id_referensi' => $approval->id_referensi,
                                'role_id' => $role->id,
                                'status' => $existingLog->status
                            ]);
                        }
                    } else {
                        // Buat approval log untuk admin gudang kategori dengan status MENUNGGU
                        // agar muncul di daftar "Proses Disposisi"
                        $newLog = ApprovalLog::create([
                            'modul_approval' => $approval->modul_approval,
                            'id_referensi' => $approval->id_referensi,
                            'id_approval_flow' => $disposisiFlow->id,
                            'user_id' => null, // Belum ada user yang memproses
                            'role_id' => $role->id,
                            'status' => 'MENUNGGU', // Status MENUNGGU agar muncul di daftar Proses Disposisi
                            'catatan' => 'Disposisi untuk kategori: ' . $kategori . ' oleh ' . $user->name,
                            'approved_at' => null, // Belum diproses
                        ]);
                        
                        \Log::info('Approval log created for disposisi', [
                            'approval_log_id' => $newLog->id,
                            'id_referensi' => $approval->id_referensi,
                            'role_id' => $role->id,
                            'role_name' => $role->name,
                            'kategori' => $kategori,
                            'step_order' => $disposisiFlow->step_order,
                            'status' => $newLog->status
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('transaction.approval.show', $id)
                ->with('success', 'Permintaan telah didisposisikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error disposisi approval: ' . $e->getMessage());
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
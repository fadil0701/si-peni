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
use App\Models\MasterPegawai;
use App\Models\Role;
use App\Models\DataInventory;

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
            $query = ApprovalLog::with(['approvalFlow.role', 'user'])
                ->where('modul_approval', 'PERMINTAAN_BARANG')
                ->whereIn('status', ['MENUNGGU', 'DIKETAHUI', 'DIVERIFIKASI', 'DIDISPOSISIKAN']);
        } else {
            // Ambil approval log yang menunggu persetujuan untuk role user saat ini
            // Gunakan whereIn untuk id_approval_flow yang sesuai dengan role user
            if ($flowDefinitions->isEmpty()) {
                // Jika tidak ada flow definition yang sesuai, tidak tampilkan apa-apa
                $query = ApprovalLog::with(['approvalFlow.role', 'user'])
                    ->where('modul_approval', 'PERMINTAAN_BARANG')
                    ->whereRaw('1 = 0'); // Tidak tampilkan apa-apa
            } else {
                $query = ApprovalLog::with(['approvalFlow.role', 'user'])
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
        
        // Ambil semua approval log untuk menentukan status per permintaan
        $allApprovals = $query->with(['approvalFlow' => function($q) {
            $q->with('role');
        }])->get();
        
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
                foreach ($group['approvals'] as $approval) {
                    $stepOrder = $approval->approvalFlow->step_order ?? 999;
                    
                // Jika status sudah diselesaikan (bukan MENUNGGU), update last completed step
                // Catatan: DIDISPOSISIKAN dan DIPROSES juga dianggap sebagai completed
                if (in_array($approval->status, ['DIKETAHUI', 'DIVERIFIKASI', 'DISETUJUI', 'DIDISPOSISIKAN', 'DIPROSES'])) {
                    if ($stepOrder > $maxCompletedStep) {
                        $maxCompletedStep = $stepOrder;
                        $lastCompletedStep = $stepOrder;
                    }
                }
                
                // Untuk step 5 (disposisi), jika status MENUNGGU, ini adalah current step
                if ($stepOrder == 5 && $approval->status === 'MENUNGGU' && !$currentStep) {
                    $currentStep = $approval;
                    $currentStatus = 'MENUNGGU';
                }
                    
                    // Jika status masih MENUNGGU, ini adalah current step
                    if ($approval->status === 'MENUNGGU' && !$currentStep) {
                        $currentStep = $approval;
                        $currentStatus = 'MENUNGGU';
                    }
                }
                
                // Jika tidak ada yang menunggu, gunakan approval terakhir dengan status terbaru
                if (!$currentStep) {
                    $currentStep = $group['latest_approval'];
                    if ($currentStep) {
                        $currentStatus = $currentStep->status;
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
        
        // Cek apakah step sebelumnya sudah diverifikasi (untuk kepala_pusat - step 4)
        $previousStepVerified = true;
        $stepOrder = $currentFlow->step_order ?? 0;
        // Untuk admin, previousStepVerified selalu true (bisa approve meski step sebelumnya belum diverifikasi)
        if ($stepOrder == 4 && !$user->hasRole('admin')) {
            // Untuk step 4 (kepala_pusat), cek apakah step 3 (kasubbag_tu) sudah diverifikasi
            $step3Flow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                ->where('step_order', 3)
                ->first();
            if ($step3Flow) {
                $step3Log = ApprovalLog::where('modul_approval', 'PERMINTAAN_BARANG')
                    ->where('id_referensi', $approval->id_referensi)
                    ->where('id_approval_flow', $step3Flow->id)
                    ->first();
                $previousStepVerified = $step3Log && $step3Log->status === 'DIVERIFIKASI';
            }
        }
        
        return view('transaction.approval.show', compact('approval', 'permintaan', 'approvalHistory', 'currentFlow', 'nextFlow', 'previousStepVerified', 'displayStatus', 'rejectedApproval'));
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
        ]);
        
        DB::beginTransaction();
        try {
            // Update approval log
            $approval->update([
                'status' => 'DIVERIFIKASI',
                'catatan' => $validated['catatan'] ?? null,
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);
            
            // Buat approval log untuk step berikutnya (Kepala Pusat)
            $nextFlow = $approval->approvalFlow->getNextStep();
            if ($nextFlow) {
                ApprovalLog::create([
                    'modul_approval' => $approval->modul_approval,
                    'id_referensi' => $approval->id_referensi,
                    'id_approval_flow' => $nextFlow->id,
                    'user_id' => null,
                    'role_id' => $nextFlow->role_id,
                    'status' => 'MENUNGGU',
                    'catatan' => null,
                    'approved_at' => null,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('transaction.approval.show', $id)
                ->with('success', 'Permintaan telah diverifikasi.');
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
        $approval = ApprovalLog::with('approvalFlow')->findOrFail($id);
        $user = Auth::user();
        
        // Validasi permission - Admin atau user dengan permission disposisi
        if (!$user->hasRole('admin') && !\App\Helpers\PermissionHelper::canAccess($user, 'transaction.approval.disposisi')) {
            abort(403, 'Anda tidak memiliki hak untuk mendisposisikan permintaan ini.');
        }
        
        // Validasi bahwa permintaan sudah disetujui oleh Kepala Pusat
        $permintaan = PermintaanBarang::find($approval->id_referensi);
        if (!$permintaan || $permintaan->status_permintaan !== 'DISETUJUI_PIMPINAN') {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Permintaan harus disetujui oleh Kepala Pusat terlebih dahulu sebelum didisposisikan.');
        }

        DB::beginTransaction();
        try {
            // Update approval log disposisi
            $approval->update([
                'status' => 'DIDISPOSISIKAN',
                'catatan' => 'Disposisi oleh Admin Gudang/Pengurus Barang',
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);
            
            // Hapus approval log untuk admin_gudang yang dibuat saat kepala pusat approve (jika ada)
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
            
            // Tentukan kategori gudang yang terlibat berdasarkan jenis_permintaan yang sudah dipilih user
            // jenis_permintaan sudah berisi array: ["ASET", "PERSEDIAAN", "FARMASI"]
            $jenisPermintaan = is_array($permintaan->jenis_permintaan) 
                ? $permintaan->jenis_permintaan 
                : json_decode($permintaan->jenis_permintaan, true) ?? [];
            
            // Langsung gunakan jenis_permintaan sebagai kategori gudang
            // Filter hanya yang valid (ASET, PERSEDIAAN, FARMASI)
            $kategoriGudang = array_filter($jenisPermintaan, function($kategori) {
                return in_array($kategori, ['ASET', 'PERSEDIAAN', 'FARMASI']);
            });
            
            // Hapus duplikat dan re-index array
            $kategoriGudang = array_values(array_unique($kategoriGudang));
            
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
                
                if ($role) {
                    // Cari atau buat flow definition untuk step disposisi dengan role ini
                    $disposisiFlow = ApprovalFlowDefinition::where('modul_approval', 'PERMINTAAN_BARANG')
                        ->where('step_order', 5)
                        ->where('role_id', $role->id)
                        ->first();
                    
                    // Jika belum ada flow definition untuk role ini, buat baru
                    if (!$disposisiFlow) {
                        $disposisiFlow = ApprovalFlowDefinition::create([
                            'modul_approval' => 'PERMINTAAN_BARANG',
                            'step_order' => 5,
                            'role_id' => $role->id,
                            'nama_step' => 'Didisposisikan - ' . $kategori,
                            'status' => 'MENUNGGU',
                            'status_text' => 'Permintaan telah didisposisikan ke Admin Gudang ' . $kategori,
                            'is_required' => true,
                            'can_reject' => false,
                            'can_approve' => false,
                        ]);
                    }
                    
                    // Pastikan tidak ada approval log yang sudah ada untuk step ini dengan role ini
                    $existingLog = ApprovalLog::where('modul_approval', $approval->modul_approval)
                        ->where('id_referensi', $approval->id_referensi)
                        ->where('id_approval_flow', $disposisiFlow->id)
                        ->first();
                    
                    if (!$existingLog) {
                        ApprovalLog::create([
                            'modul_approval' => $approval->modul_approval,
                            'id_referensi' => $approval->id_referensi,
                            'id_approval_flow' => $disposisiFlow->id,
                            'user_id' => null,
                            'role_id' => $role->id,
                            'status' => 'MENUNGGU',
                            'catatan' => 'Disposisi untuk kategori: ' . $kategori,
                            'approved_at' => null,
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
<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovalPermintaan;
use App\Models\PermintaanBarang;
use App\Models\MasterPegawai;

class ApprovalPermintaanController extends Controller
{
    public function index(Request $request)
    {
        $query = ApprovalPermintaan::with(['approver'])
            ->where('modul_approval', 'PERMINTAAN_BARANG');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_approval', $request->status);
        }

        // Filter hanya yang menunggu approval
        if ($request->filled('menunggu')) {
            $query->where('status_approval', 'MENUNGGU');
        }

        $approvals = $query->latest('created_at')->paginate(15);

        return view('transaction.approval.index', compact('approvals'));
    }

    public function show($id)
    {
        $approval = ApprovalPermintaan::with(['approver'])
            ->findOrFail($id);

        // Load permintaan berdasarkan modul dan id_referensi
        $permintaan = null;
        if ($approval->modul_approval === 'PERMINTAAN_BARANG') {
            $permintaan = PermintaanBarang::with([
                'unitKerja', 
                'pemohon.jabatan', 
                'detailPermintaan.dataBarang', 
                'detailPermintaan.satuan'
            ])->find($approval->id_referensi);
        }

        return view('transaction.approval.show', compact('approval', 'permintaan'));
    }

    public function approve(Request $request, $id)
    {
        $approval = ApprovalPermintaan::findOrFail($id);

        if ($approval->status_approval !== 'MENUNGGU') {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Approval ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'catatan' => 'nullable|string',
        ]);

        $approval->update([
            'status_approval' => 'DISETUJUI',
            'catatan' => $validated['catatan'] ?? null,
            'tanggal_approval' => now(),
            'id_approver' => auth()->user()->id, // Asumsi menggunakan user yang login sebagai approver
        ]);

        // Update status permintaan menjadi DISETUJUI
        if ($approval->modul_approval === 'PERMINTAAN_BARANG') {
            $permintaan = PermintaanBarang::find($approval->id_referensi);
            if ($permintaan) {
                $permintaan->update(['status_permintaan' => 'DISETUJUI']);
            }
        }

        return redirect()->route('transaction.approval.show', $id)
            ->with('success', 'Permintaan berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $approval = ApprovalPermintaan::findOrFail($id);

        if ($approval->status_approval !== 'MENUNGGU') {
            return redirect()->route('transaction.approval.show', $id)
                ->with('error', 'Approval ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'catatan' => 'required|string',
        ]);

        $approval->update([
            'status_approval' => 'DITOLAK',
            'catatan' => $validated['catatan'],
            'tanggal_approval' => now(),
            'id_approver' => auth()->user()->id,
        ]);

        // Update status permintaan menjadi DITOLAK
        if ($approval->modul_approval === 'PERMINTAAN_BARANG') {
            $permintaan = PermintaanBarang::find($approval->id_referensi);
            if ($permintaan) {
                $permintaan->update(['status_permintaan' => 'DITOLAK']);
            }
        }

        return redirect()->route('transaction.approval.show', $id)
            ->with('success', 'Permintaan ditolak.');
    }
}

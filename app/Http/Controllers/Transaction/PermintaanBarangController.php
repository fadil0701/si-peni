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
use Carbon\Carbon;

class PermintaanBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = PermintaanBarang::with(['unitKerja', 'pemohon']);

        // Filters
        if ($request->filled('unit_kerja')) {
            $query->where('id_unit_kerja', $request->unit_kerja);
        }

        if ($request->filled('status')) {
            $query->where('status_permintaan', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_permintaan', $request->jenis);
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

        $permintaans = $query->latest('tanggal_permintaan')->paginate(15);
        $unitKerjas = MasterUnitKerja::all();

        return view('transaction.permintaan-barang.index', compact('permintaans', 'unitKerjas'));
    }

    public function create()
    {
        $unitKerjas = MasterUnitKerja::all();
        $pegawais = MasterPegawai::all();
        $dataBarangs = MasterDataBarang::with(['subjenisBarang', 'satuan'])->get();
        $satuans = MasterSatuan::all();

        return view('transaction.permintaan-barang.create', compact('unitKerjas', 'pegawais', 'dataBarangs', 'satuans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_unit_kerja' => 'required|exists:master_unit_kerja,id_unit_kerja',
            'id_pemohon' => 'required|exists:master_pegawai,id',
            'tanggal_permintaan' => 'required|date',
            'jenis_permintaan' => 'required|in:BARANG,ASET',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.id_data_barang' => 'required|exists:master_data_barang,id_data_barang',
            'detail.*.qty_diminta' => 'required|numeric|min:0.01',
            'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
            'detail.*.keterangan' => 'nullable|string',
        ]);

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
            $permintaan = PermintaanBarang::create([
                'no_permintaan' => $noPermintaan,
                'id_unit_kerja' => $validated['id_unit_kerja'],
                'id_pemohon' => $validated['id_pemohon'],
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'jenis_permintaan' => $validated['jenis_permintaan'],
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
        $permintaan = PermintaanBarang::with('detailPermintaan')->findOrFail($id);
        
        // Hanya bisa edit jika status DRAFT
        if ($permintaan->status_permintaan !== 'DRAFT') {
            return redirect()->route('transaction.permintaan-barang.show', $id)
                ->with('error', 'Permintaan yang sudah diajukan tidak dapat di-edit.');
        }

        $unitKerjas = MasterUnitKerja::all();
        $pegawais = MasterPegawai::all();
        $dataBarangs = MasterDataBarang::with(['subjenisBarang', 'satuan'])->get();
        $satuans = MasterSatuan::all();

        return view('transaction.permintaan-barang.edit', compact('permintaan', 'unitKerjas', 'pegawais', 'dataBarangs', 'satuans'));
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
            'jenis_permintaan' => 'required|in:BARANG,ASET',
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
            $permintaan->update([
                'id_unit_kerja' => $validated['id_unit_kerja'],
                'id_pemohon' => $validated['id_pemohon'],
                'tanggal_permintaan' => $validated['tanggal_permintaan'],
                'jenis_permintaan' => $validated['jenis_permintaan'],
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

            // Buat record approval
            \App\Models\ApprovalPermintaan::create([
                'modul_approval' => 'PERMINTAAN_BARANG',
                'id_referensi' => $permintaan->id_permintaan,
                'id_approver' => auth()->user()->id, // TODO: Ambil dari kepala unit kerja atau role
                'status_approval' => 'MENUNGGU',
                'catatan' => null,
                'tanggal_approval' => null,
            ]);

            DB::commit();

            return redirect()->route('transaction.permintaan-barang.show', $id)
                ->with('success', 'Permintaan berhasil diajukan untuk persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error mengajukan permintaan: ' . $e->getMessage());
            return redirect()->route('transaction.permintaan-barang.show', $id)
                ->with('error', 'Terjadi kesalahan saat mengajukan permintaan: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PenerimaanBarang;
use App\Models\DetailPenerimaanBarang;
use App\Models\TransaksiDistribusi;
use App\Models\MasterUnitKerja;
use App\Models\MasterPegawai;
use App\Models\MasterSatuan;
use Carbon\Carbon;

class PenerimaanBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = PenerimaanBarang::with(['distribusi', 'unitKerja', 'pegawaiPenerima']);

        // Filters
        if ($request->filled('unit_kerja')) {
            $query->where('id_unit_kerja', $request->unit_kerja);
        }

        if ($request->filled('status')) {
            $query->where('status_penerimaan', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_penerimaan', 'like', "%{$search}%")
                  ->orWhereHas('distribusi', function($q) use ($search) {
                      $q->where('no_sbbk', 'like', "%{$search}%");
                  });
            });
        }

        $penerimaans = $query->latest('tanggal_penerimaan')->paginate(15);
        $unitKerjas = MasterUnitKerja::all();

        return view('transaction.penerimaan-barang.index', compact('penerimaans', 'unitKerjas'));
    }

    public function create(Request $request)
    {
        // Filter distribusi yang sudah dikirim dan belum diterima
        $distribusis = TransaksiDistribusi::where('status_distribusi', 'DIKIRIM')
            ->whereDoesntHave('penerimaanBarang', function($q) {
                $q->where('status_penerimaan', 'DITERIMA');
            })
            ->with(['gudangAsal', 'gudangTujuan', 'permintaan.unitKerja'])
            ->get();

        $unitKerjas = MasterUnitKerja::all();
        $pegawais = MasterPegawai::all();
        $satuans = MasterSatuan::all();

        // Jika ada distribusi_id di request, load detail distribusi
        $selectedDistribusi = null;
        if ($request->filled('distribusi_id')) {
            $selectedDistribusi = TransaksiDistribusi::with([
                'detailDistribusi.inventory.dataBarang',
                'detailDistribusi.satuan',
                'gudangTujuan.unitKerja'
            ])->find($request->distribusi_id);
        }

        return view('transaction.penerimaan-barang.create', compact('distribusis', 'unitKerjas', 'pegawais', 'satuans', 'selectedDistribusi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_distribusi' => 'required|exists:transaksi_distribusi,id_distribusi',
            'tanggal_penerimaan' => 'required|date',
            'id_unit_kerja' => 'required|exists:master_unit_kerja,id_unit_kerja',
            'id_pegawai_penerima' => 'required|exists:master_pegawai,id',
            'status_penerimaan' => 'required|in:DITERIMA,DITOLAK',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.id_inventory' => 'required|exists:data_inventory,id_inventory',
            'detail.*.qty_diterima' => 'required|numeric|min:0',
            'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
            'detail.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate nomor penerimaan
            $tahun = Carbon::parse($validated['tanggal_penerimaan'])->format('Y');
            $lastPenerimaan = PenerimaanBarang::whereYear('tanggal_penerimaan', $tahun)
                ->orderBy('no_penerimaan', 'desc')
                ->first();

            $urut = 1;
            if ($lastPenerimaan) {
                $parts = explode('/', $lastPenerimaan->no_penerimaan);
                $urut = (int)end($parts) + 1;
            }

            $noPenerimaan = sprintf('TERIMA/%s/%04d', $tahun, $urut);

            // Create penerimaan
            $penerimaan = PenerimaanBarang::create([
                'no_penerimaan' => $noPenerimaan,
                'id_distribusi' => $validated['id_distribusi'],
                'id_unit_kerja' => $validated['id_unit_kerja'],
                'id_pegawai_penerima' => $validated['id_pegawai_penerima'],
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'status_penerimaan' => $validated['status_penerimaan'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Create detail penerimaan
            foreach ($validated['detail'] as $detail) {
                DetailPenerimaanBarang::create([
                    'id_penerimaan' => $penerimaan->id_penerimaan,
                    'id_inventory' => $detail['id_inventory'],
                    'qty_diterima' => $detail['qty_diterima'],
                    'id_satuan' => $detail['id_satuan'],
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            // Update status distribusi menjadi SELESAI jika diterima
            if ($validated['status_penerimaan'] === 'DITERIMA') {
                $distribusi = TransaksiDistribusi::find($validated['id_distribusi']);
                $distribusi->update(['status_distribusi' => 'SELESAI']);
            }

            DB::commit();

            return redirect()->route('transaction.penerimaan-barang.index')
                ->with('success', 'Penerimaan barang berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating penerimaan barang: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $penerimaan = PenerimaanBarang::with([
            'distribusi.gudangAsal',
            'distribusi.gudangTujuan',
            'distribusi.permintaan',
            'unitKerja',
            'pegawaiPenerima',
            'detailPenerimaan.inventory.dataBarang',
            'detailPenerimaan.satuan'
        ])->findOrFail($id);

        return view('transaction.penerimaan-barang.show', compact('penerimaan'));
    }

    public function edit($id)
    {
        $penerimaan = PenerimaanBarang::with('detailPenerimaan')->findOrFail($id);
        
        // Hanya bisa edit jika status DITERIMA (belum final)
        // Atau bisa diubah sesuai kebutuhan bisnis

        $distribusis = TransaksiDistribusi::where('status_distribusi', 'DIKIRIM')->get();
        $unitKerjas = MasterUnitKerja::all();
        $pegawais = MasterPegawai::all();
        $satuans = MasterSatuan::all();

        return view('transaction.penerimaan-barang.edit', compact('penerimaan', 'distribusis', 'unitKerjas', 'pegawais', 'satuans'));
    }

    public function update(Request $request, $id)
    {
        $penerimaan = PenerimaanBarang::findOrFail($id);

        $validated = $request->validate([
            'tanggal_penerimaan' => 'required|date',
            'id_unit_kerja' => 'required|exists:master_unit_kerja,id_unit_kerja',
            'id_pegawai_penerima' => 'required|exists:master_pegawai,id',
            'status_penerimaan' => 'required|in:DITERIMA,DITOLAK',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.id_inventory' => 'required|exists:data_inventory,id_inventory',
            'detail.*.qty_diterima' => 'required|numeric|min:0',
            'detail.*.id_satuan' => 'required|exists:master_satuan,id_satuan',
            'detail.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update penerimaan
            $penerimaan->update([
                'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                'id_unit_kerja' => $validated['id_unit_kerja'],
                'id_pegawai_penerima' => $validated['id_pegawai_penerima'],
                'status_penerimaan' => $validated['status_penerimaan'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Delete existing details
            $penerimaan->detailPenerimaan()->delete();

            // Create new details
            foreach ($validated['detail'] as $detail) {
                DetailPenerimaanBarang::create([
                    'id_penerimaan' => $penerimaan->id_penerimaan,
                    'id_inventory' => $detail['id_inventory'],
                    'qty_diterima' => $detail['qty_diterima'],
                    'id_satuan' => $detail['id_satuan'],
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            // Update status distribusi
            if ($validated['status_penerimaan'] === 'DITERIMA') {
                $penerimaan->distribusi->update(['status_distribusi' => 'SELESAI']);
            } else {
                $penerimaan->distribusi->update(['status_distribusi' => 'DIKIRIM']);
            }

            DB::commit();

            return redirect()->route('transaction.penerimaan-barang.index')
                ->with('success', 'Penerimaan barang berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating penerimaan barang: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $penerimaan = PenerimaanBarang::findOrFail($id);

        DB::beginTransaction();
        try {
            // Kembalikan status distribusi ke DIKIRIM
            $penerimaan->distribusi->update(['status_distribusi' => 'DIKIRIM']);

            $penerimaan->detailPenerimaan()->delete();
            $penerimaan->delete();

            DB::commit();

            return redirect()->route('transaction.penerimaan-barang.index')
                ->with('success', 'Penerimaan barang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting penerimaan barang: ' . $e->getMessage());
            return redirect()->route('transaction.penerimaan-barang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function getDistribusiDetail($id)
    {
        $distribusi = TransaksiDistribusi::with([
            'detailDistribusi.inventory.dataBarang',
            'detailDistribusi.satuan',
            'gudangTujuan.unitKerja'
        ])->findOrFail($id);

        $details = $distribusi->detailDistribusi->map(function($detail) {
            return [
                'id_inventory' => $detail->id_inventory,
                'nama_barang' => $detail->inventory->dataBarang->nama_barang ?? '-',
                'qty_distribusi' => $detail->qty_distribusi,
                'id_satuan' => $detail->id_satuan,
                'nama_satuan' => $detail->satuan->nama_satuan ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'distribusi' => [
                'id_distribusi' => $distribusi->id_distribusi,
                'no_sbbk' => $distribusi->no_sbbk,
                'gudang_tujuan' => $distribusi->gudangTujuan->nama_gudang ?? '-',
                'unit_kerja' => $distribusi->gudangTujuan->unitKerja->id_unit_kerja ?? null,
            ],
            'details' => $details,
        ]);
    }
}

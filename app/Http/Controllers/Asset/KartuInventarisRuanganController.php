<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KartuInventarisRuangan;
use App\Models\RegisterAset;
use App\Models\MasterRuangan;
use App\Models\MasterPegawai;
use Illuminate\Support\Facades\Auth;

class KartuInventarisRuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query KIR dengan relationships
        $query = KartuInventarisRuangan::with([
            'registerAset.inventory.dataBarang',
            'registerAset.unitKerja',
            'ruangan.unitKerja',
            'penanggungJawab'
        ]);
        
        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                $query->whereHas('ruangan', function($q) use ($pegawai) {
                    $q->where('id_unit_kerja', $pegawai->id_unit_kerja);
                });
            } else {
                $query->whereRaw('1 = 0'); // Tidak ada data jika user tidak punya unit kerja
            }
        }
        
        // Filter berdasarkan ruangan jika ada
        if ($request->filled('id_ruangan')) {
            $query->where('id_ruangan', $request->id_ruangan);
        }
        
        // Filter berdasarkan unit kerja jika ada
        if ($request->filled('id_unit_kerja')) {
            $query->whereHas('ruangan', function($q) use ($request) {
                $q->where('id_unit_kerja', $request->id_unit_kerja);
            });
        }
        
        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 15);
        $kirs = $query->latest('tanggal_penempatan')->paginate($perPage)->appends($request->query());
        
        // Data untuk filter
        $ruangans = MasterRuangan::with('unitKerja')->orderBy('nama_ruangan')->get();
        
        return view('asset.kartu-inventaris-ruangan.index', compact('kirs', 'ruangans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Hanya admin dan admin_gudang yang bisa create
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        // Ambil register aset yang belum punya KIR atau bisa dipindahkan
        $registerAsets = RegisterAset::with(['inventory.dataBarang', 'unitKerja'])
            ->where('status_aset', 'AKTIF')
            ->orderBy('nomor_register')
            ->get();
        
        // Ambil semua ruangan
        $ruangans = MasterRuangan::with('unitKerja')->orderBy('nama_ruangan')->get();
        
        // Ambil semua pegawai untuk penanggung jawab
        $pegawais = MasterPegawai::with('unitKerja')->orderBy('nama_pegawai')->get();
        
        return view('asset.kartu-inventaris-ruangan.create', compact('registerAsets', 'ruangans', 'pegawais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Hanya admin dan admin_gudang yang bisa store
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        // Validasi input
        $validated = $request->validate([
            'id_register_aset' => 'required|exists:register_aset,id_register_aset',
            'id_ruangan' => 'required|exists:master_ruangan,id_ruangan',
            'id_penanggung_jawab' => 'required|exists:master_pegawai,id',
            'tanggal_penempatan' => 'required|date',
        ]);
        
        // Cek apakah register aset sudah punya KIR
        $existingKIR = KartuInventarisRuangan::where('id_register_aset', $validated['id_register_aset'])->first();
        if ($existingKIR) {
            return back()->withErrors(['id_register_aset' => 'Register aset ini sudah memiliki KIR. Gunakan Mutasi Aset untuk memindahkan.'])->withInput();
        }
        
        // Update inventory_item untuk set id_ruangan
        $registerAset = RegisterAset::findOrFail($validated['id_register_aset']);
        if ($registerAset->inventory) {
            // Update inventory item yang terkait
            \App\Models\InventoryItem::where('id_inventory', $registerAset->inventory->id_inventory)
                ->update(['id_ruangan' => $validated['id_ruangan']]);
        }
        
        // Buat KIR
        $kir = KartuInventarisRuangan::create($validated);
        
        return redirect()->route('asset.kartu-inventaris-ruangan.show', $kir->id_kir)
            ->with('success', 'Kartu Inventaris Ruangan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kir = KartuInventarisRuangan::with([
            'registerAset.inventory.dataBarang',
            'registerAset.unitKerja',
            'ruangan.unitKerja',
            'penanggungJawab.unitKerja',
            'penanggungJawab.jabatan'
        ])->findOrFail($id);
        
        $user = Auth::user();
        
        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($kir->ruangan->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat melihat KIR dari unit kerja Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki unit kerja');
            }
        }
        
        return view('asset.kartu-inventaris-ruangan.show', compact('kir'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kir = KartuInventarisRuangan::with(['registerAset', 'ruangan', 'penanggungJawab'])->findOrFail($id);
        $user = Auth::user();
        
        // Hanya admin dan admin_gudang yang bisa edit
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($kir->ruangan->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat mengedit KIR dari unit kerja Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki unit kerja');
            }
        }
        
        // Ambil semua ruangan
        $ruangans = MasterRuangan::with('unitKerja')->orderBy('nama_ruangan')->get();
        
        // Ambil semua pegawai untuk penanggung jawab
        $pegawais = MasterPegawai::with('unitKerja')->orderBy('nama_pegawai')->get();
        
        return view('asset.kartu-inventaris-ruangan.edit', compact('kir', 'ruangans', 'pegawais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kir = KartuInventarisRuangan::findOrFail($id);
        $user = Auth::user();
        
        // Hanya admin dan admin_gudang yang bisa update
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($kir->ruangan->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat mengupdate KIR dari unit kerja Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki unit kerja');
            }
        }
        
        // Validasi input
        $validated = $request->validate([
            'id_ruangan' => 'required|exists:master_ruangan,id_ruangan',
            'id_penanggung_jawab' => 'required|exists:master_pegawai,id',
            'tanggal_penempatan' => 'required|date',
        ]);
        
        // Jika ruangan berubah, update inventory_item juga
        if ($kir->id_ruangan != $validated['id_ruangan']) {
            $registerAset = $kir->registerAset;
            if ($registerAset && $registerAset->inventory) {
                \App\Models\InventoryItem::where('id_inventory', $registerAset->inventory->id_inventory)
                    ->update(['id_ruangan' => $validated['id_ruangan']]);
            }
        }
        
        // Update KIR
        $kir->update($validated);
        
        return redirect()->route('asset.kartu-inventaris-ruangan.show', $kir->id_kir)
            ->with('success', 'Kartu Inventaris Ruangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Hanya admin dan admin_gudang yang bisa delete
        if (!Auth::user()->hasAnyRole(['admin', 'admin_gudang'])) {
            abort(403, 'Unauthorized');
        }
        
        $kir = KartuInventarisRuangan::findOrFail($id);
        
        // Update inventory_item untuk remove id_ruangan
        $registerAset = $kir->registerAset;
        if ($registerAset && $registerAset->inventory) {
            \App\Models\InventoryItem::where('id_inventory', $registerAset->inventory->id_inventory)
                ->update(['id_ruangan' => null]);
        }
        
        $kir->delete();
        
        return redirect()->route('asset.kartu-inventaris-ruangan.index')
            ->with('success', 'Kartu Inventaris Ruangan berhasil dihapus.');
    }
}

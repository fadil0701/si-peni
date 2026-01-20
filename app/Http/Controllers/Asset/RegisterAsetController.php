<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegisterAset;
use App\Models\MasterPegawai;
use Illuminate\Support\Facades\Auth;

class RegisterAsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = RegisterAset::with(['inventory', 'unitKerja']);

        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                $query->where('id_unit_kerja', $pegawai->id_unit_kerja);
            } else {
                // Jika user tidak memiliki pegawai atau unit kerja, tidak tampilkan data
                $query->whereRaw('1 = 0');
            }
        }

        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $registerAsets = $query->latest()->paginate($perPage)->appends($request->query());
        
        return view('asset.register-aset.index', compact('registerAsets'));
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
        
        // TODO: Implement create view
        return view('asset.register-aset.create');
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
        
        // TODO: Implement store logic
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $registerAset = RegisterAset::with(['inventory', 'unitKerja'])->findOrFail($id);
        $user = Auth::user();

        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($registerAset->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat melihat aset dari unit kerja Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki unit kerja');
            }
        }

        return view('asset.register-aset.show', compact('registerAset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $registerAset = RegisterAset::with(['inventory', 'unitKerja'])->findOrFail($id);
        $user = Auth::user();

        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($registerAset->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat mengedit aset dari unit kerja Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki unit kerja');
            }
        }

        return view('asset.register-aset.edit', compact('registerAset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $registerAset = RegisterAset::findOrFail($id);
        $user = Auth::user();

        // Filter berdasarkan unit kerja untuk kepala_unit dan pegawai
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                if ($registerAset->id_unit_kerja != $pegawai->id_unit_kerja) {
                    abort(403, 'Unauthorized - Anda hanya dapat mengupdate aset dari unit kerja Anda sendiri');
                }
            } else {
                abort(403, 'Unauthorized - User tidak memiliki unit kerja');
            }
        }

        // TODO: Implement update logic
        $validated = $request->validate([
            'kondisi_aset' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT',
            'status_aset' => 'required|in:AKTIF,NONAKTIF',
            'tanggal_perolehan' => 'nullable|date',
        ]);

        $registerAset->update($validated);

        return redirect()->route('asset.register-aset.index')
            ->with('success', 'Register aset berhasil diperbarui.');
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
        
        $registerAset = RegisterAset::findOrFail($id);
        $registerAset->delete();

        return redirect()->route('asset.register-aset.index')
            ->with('success', 'Register aset berhasil dihapus.');
    }
}

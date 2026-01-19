<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterKegiatan;
use App\Models\MasterProgram;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = MasterKegiatan::with('program')->latest()->paginate(15);
        return view('master.kegiatan.index', compact('kegiatans'));
    }

    public function create()
    {
        $programs = MasterProgram::all();
        return view('master.kegiatan.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_program' => 'required|exists:master_program,id_program',
            'nama_kegiatan' => 'required|string|max:255',
        ]);

        MasterKegiatan::create($validated);

        return redirect()->route('master.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $kegiatan = MasterKegiatan::with('program')->findOrFail($id);
        return view('master.kegiatan.show', compact('kegiatan'));
    }

    public function edit($id)
    {
        $kegiatan = MasterKegiatan::findOrFail($id);
        $programs = MasterProgram::all();
        return view('master.kegiatan.edit', compact('kegiatan', 'programs'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = MasterKegiatan::findOrFail($id);

        $validated = $request->validate([
            'id_program' => 'required|exists:master_program,id_program',
            'nama_kegiatan' => 'required|string|max:255',
        ]);

        $kegiatan->update($validated);

        return redirect()->route('master.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kegiatan = MasterKegiatan::findOrFail($id);
        $kegiatan->delete();

        return redirect()->route('master.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }
}

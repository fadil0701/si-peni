<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanBarang;
use App\Models\MasterDataBarang;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $requests = PermintaanBarang::with('user')
            ->latest()
            ->paginate($perPage)->appends($request->query());

        return view('user.requests.index', compact('requests'));
    }

    public function create()
    {
        $barangs = MasterDataBarang::all();
        return view('user.requests.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_data_barang' => 'required|exists:master_data_barang,id_data_barang',
            'qty_permintaan' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $validated['id_user'] = auth()->id();
        $validated['tanggal_permintaan'] = now();
        $validated['status_permintaan'] = 'DIAJUKAN';

        PermintaanBarang::create($validated);

        return redirect()->route('user.requests')
            ->with('success', 'Permintaan berhasil diajukan.');
    }

    public function show($id)
    {
        $request = PermintaanBarang::with(['user', 'detailPermintaan.dataBarang'])
            ->findOrFail($id);

        return view('user.requests.show', compact('request'));
    }
}


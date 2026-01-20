<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataStock;
use App\Models\MasterGudang;
use App\Models\MasterDataBarang;
use App\Models\MasterPegawai;
use Illuminate\Support\Facades\Auth;

class DataStockController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // GUDANG PUSAT (Admin/Admin Gudang): Melihat SEMUA data stock (global view)
        // GUDANG UNIT (Kepala Unit/Admin Unit): Hanya melihat stock di unitnya saja (local view)
        
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            // GUDANG UNIT: Hanya melihat stock yang ada di gudang UNIT mereka
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                $query = DataStock::with(['dataBarang', 'gudang', 'satuan'])
                    ->whereHas('gudang', function ($q) use ($pegawai) {
                        $q->where('jenis_gudang', 'UNIT')
                          ->where('id_unit_kerja', $pegawai->id_unit_kerja);
                    });
            } else {
                // Jika user tidak memiliki pegawai atau unit kerja, tidak tampilkan data
                $query = DataStock::whereRaw('1 = 0');
            }
        } else {
            // GUDANG PUSAT: Melihat SEMUA data stock (tidak ada filter)
            $query = DataStock::with(['dataBarang', 'gudang', 'satuan']);
        }

        // Filters
        if ($request->filled('gudang')) {
            $query->where('id_gudang', $request->gudang);
        }

        if ($request->filled('jenis')) {
            // Filter by jenis inventory through data_barang
            $query->whereHas('dataBarang', function ($q) use ($request) {
                // This would need to check through inventory relationship
            });
        }

        if ($request->filled('sub_kategori')) {
            // Filter by sub kategori through data_barang
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('dataBarang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_data_barang', 'like', "%{$search}%");
            });
        }

        $perPage = \App\Helpers\PaginationHelper::getPerPage($request, 10);
        $stocks = $query->latest('last_updated')->paginate($perPage)->appends($request->query());
        
        // Filter gudang yang ditampilkan di dropdown berdasarkan role
        if ($user->hasAnyRole(['kepala_unit', 'pegawai']) && !$user->hasRole('admin')) {
            $pegawai = MasterPegawai::where('user_id', $user->id)->first();
            if ($pegawai && $pegawai->id_unit_kerja) {
                $gudangs = MasterGudang::where('jenis_gudang', 'UNIT')
                    ->where('id_unit_kerja', $pegawai->id_unit_kerja)
                    ->get();
            } else {
                $gudangs = collect([]);
            }
        } else {
            $gudangs = MasterGudang::all();
        }

        return view('inventory.data-stock.index', compact('stocks', 'gudangs'));
    }
}


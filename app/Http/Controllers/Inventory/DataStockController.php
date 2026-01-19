<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataStock;
use App\Models\MasterGudang;
use App\Models\MasterDataBarang;

class DataStockController extends Controller
{
    public function index(Request $request)
    {
        $query = DataStock::with(['dataBarang', 'gudang', 'satuan']);

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

        $stocks = $query->latest('last_updated')->paginate(15);
        $gudangs = MasterGudang::all();

        return view('inventory.data-stock.index', compact('stocks', 'gudangs'));
    }
}


<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\PermintaanBarang;
use App\Models\DataStock;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get statistics
        $totalAssets = InventoryItem::whereHas('inventory', function ($query) {
            $query->where('jenis_inventory', 'ASET');
        })->count();

        $totalStock = DataStock::sum('qty_akhir');

        $activeRequests = PermintaanBarang::whereIn('status_permintaan', ['DIAJUKAN', 'DISETUJUI'])
            ->count();

        // Get latest requests - sesuai ERD: permintaan_barang dengan id_pemohon
        $latestRequests = PermintaanBarang::latest('tanggal_permintaan')
            ->limit(5)
            ->with('pemohon')
            ->get();

        // Get latest assets - sesuai ERD: inventory_item -> data_inventory -> master_data_barang
        $latestAssets = InventoryItem::whereHas('inventory', function ($query) {
            $query->where('jenis_inventory', 'ASET');
        })
        ->with(['inventory.dataBarang', 'ruangan'])
        ->latest('created_at')
        ->limit(5)
        ->get();

        // Get latest transactions - dari transaksi_distribusi dan penerimaan_barang
        $latestDistribusi = \App\Models\TransaksiDistribusi::latest('tanggal_distribusi')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->no_sbbk,
                    'jenis' => 'Distribusi (SBBK)',
                    'tanggal' => $item->tanggal_distribusi,
                ];
            });

        $latestPenerimaan = \App\Models\PenerimaanBarang::latest('tanggal_penerimaan')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->no_penerimaan,
                    'jenis' => 'Penerimaan',
                    'tanggal' => $item->tanggal_penerimaan,
                ];
            });

        $latestTransactions = $latestDistribusi->concat($latestPenerimaan)
            ->sortByDesc('tanggal')
            ->take(5)
            ->values();

        // Get request status data for chart
        $requestStatusData = [
            'diajukan' => PermintaanBarang::where('status_permintaan', 'DIAJUKAN')->count(),
            'disetujui' => PermintaanBarang::where('status_permintaan', 'DISETUJUI')->count(),
            'dikirim' => PermintaanBarang::where('status_permintaan', 'DISETUJUI')
                ->whereHas('transaksiDistribusi', function ($query) {
                    $query->whereIn('status_distribusi', ['DIKIRIM', 'SELESAI']);
                })
                ->count(),
            'ditolak' => PermintaanBarang::where('status_permintaan', 'DITOLAK')->count(),
        ];

        return view('user.dashboard', compact(
            'totalAssets',
            'totalStock',
            'activeRequests',
            'latestRequests',
            'latestAssets',
            'latestTransactions',
            'requestStatusData'
        ));
    }
}

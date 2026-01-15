<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use App\Models\DataStock;
use App\Models\PermintaanBarang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAset = InventoryItem::whereHas('inventory', function ($query) {
            $query->where('jenis_inventory', 'ASET');
        })->count();
        
        $stokGudang = DataStock::sum('qty_akhir');
        
        $permintaanAktif = PermintaanBarang::whereIn('status_permintaan', ['DIAJUKAN', 'DISETUJUI'])->count();
        
        // TODO: Tambahkan model untuk Audit/BMD jika sudah ada
        $auditBMD = 0; // Placeholder

        return [
            Stat::make('Total Aset', number_format($totalAset, 0, ',', '.') . ' Unit')
                ->description('Aset terdaftar di sistem')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
            
            Stat::make('Stok Gudang', number_format($stokGudang, 0, ',', '.') . ' Item')
                ->description('Total stok di semua gudang')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('success'),
            
            Stat::make('Permintaan Aktif', $permintaanAktif . ' Pengajuan')
                ->description('Permintaan yang sedang diproses')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning'),
            
            Stat::make('Audit / BMD', $auditBMD . ' Jadwal Audit')
                ->description('Jadwal audit yang akan datang')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('purple'),
        ];
    }
}

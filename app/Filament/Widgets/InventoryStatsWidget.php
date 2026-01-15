<?php

namespace App\Filament\Widgets;

use App\Models\DataInventory;
use App\Models\DataStock;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAset = DataInventory::where('jenis_inventory', 'ASET')->sum('qty_input');
        $totalPersediaan = DataInventory::where('jenis_inventory', 'PERSEDIAAN')->sum('qty_input');
        $totalStock = DataStock::sum('qty_akhir');
        $gudangAktif = DataStock::distinct('id_gudang')->count('id_gudang');

        return [
            Stat::make('Total Aset', number_format($totalAset, 0, ',', '.'))
                ->description('Unit aset terdaftar')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
            
            Stat::make('Total Persediaan', number_format($totalPersediaan, 0, ',', '.'))
                ->description('Unit persediaan')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('success'),
            
            Stat::make('Total Stock', number_format($totalStock, 0, ',', '.'))
                ->description('Stock di semua gudang')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
            
            Stat::make('Gudang Aktif', $gudangAktif)
                ->description('Gudang dengan stock')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning'),
        ];
    }
}


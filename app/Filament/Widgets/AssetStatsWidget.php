<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use App\Models\RegisterAset;
use App\Models\MutasiAset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalAset = InventoryItem::whereHas('inventory', function ($query) {
            $query->where('jenis_inventory', 'ASET');
        })->count();
        
        $asetAktif = InventoryItem::whereHas('inventory', function ($query) {
            $query->where('jenis_inventory', 'ASET');
        })->where('status_item', 'AKTIF')->count();
        
        $asetTerdaftar = RegisterAset::count();
        $mutasiBulanIni = MutasiAset::whereMonth('tanggal_mutasi', now()->month)
            ->whereYear('tanggal_mutasi', now()->year)
            ->count();

        return [
            Stat::make('Total Aset', number_format($totalAset, 0, ',', '.'))
                ->description('Semua aset terdaftar')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
            
            Stat::make('Aset Aktif', number_format($asetAktif, 0, ',', '.'))
                ->description('Aset dengan status aktif')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Register Aset', number_format($asetTerdaftar, 0, ',', '.'))
                ->description('Aset yang sudah diregister')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            
            Stat::make('Mutasi Bulan Ini', $mutasiBulanIni)
                ->description('Mutasi aset bulan ini')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),
        ];
    }
}


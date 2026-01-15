<?php

namespace App\Filament\Widgets;

use App\Models\PermintaanBarang;
use App\Models\TransaksiDistribusi;
use App\Models\PenerimaanBarang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $permintaanPending = PermintaanBarang::where('status_permintaan', '=', 'DIAJUKAN')->count();
        $permintaanDisetujui = PermintaanBarang::where('status_permintaan', '=', 'DISETUJUI')->count();
        $distribusiHariIni = TransaksiDistribusi::whereDate('tanggal_distribusi', today())->count();
        $penerimaanHariIni = PenerimaanBarang::whereDate('tanggal_penerimaan', today())->count();

        return [
            Stat::make('Permintaan Pending', $permintaanPending)
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('Permintaan Disetujui', $permintaanDisetujui)
                ->description('Sudah disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Distribusi Hari Ini', $distribusiHariIni)
                ->description('Transaksi distribusi')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color('info'),
            
            Stat::make('Penerimaan Hari Ini', $penerimaanHariIni)
                ->description('Barang diterima')
                ->descriptionIcon('heroicon-m-arrow-down-circle')
                ->color('primary'),
        ];
    }
}


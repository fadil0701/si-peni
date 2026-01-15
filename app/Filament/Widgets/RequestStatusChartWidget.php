<?php

namespace App\Filament\Widgets;

use App\Models\PermintaanBarang;
use Filament\Widgets\ChartWidget;

class RequestStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Status Permintaan Barang';
    
    protected ?string $description = 'Grafik status permintaan barang';
    
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $diajukan = PermintaanBarang::where('status_permintaan', 'DIAJUKAN')->count();
        $disetujui = PermintaanBarang::where('status_permintaan', 'DISETUJUI')->count();
        $dikirim = PermintaanBarang::where('status_permintaan', 'DISETUJUI')
            ->whereHas('transaksiDistribusi', function ($query) {
                $query->whereIn('status_distribusi', ['DIKIRIM', 'SELESAI']);
            })
            ->count();
        $ditolak = PermintaanBarang::where('status_permintaan', 'DITOLAK')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permintaan',
                    'data' => [$diajukan, $disetujui, $dikirim, $ditolak],
                    'backgroundColor' => [
                        'rgb(59, 130, 246)', // Blue for Diajukan
                        'rgb(34, 197, 94)',  // Green for Disetujui
                        'rgb(249, 115, 22)', // Orange for Dikirim
                        'rgb(239, 68, 68)',  // Red for Ditolak
                    ],
                ],
            ],
            'labels' => ['Diajukan', 'Disetujui', 'Dikirim', 'Ditolak'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}


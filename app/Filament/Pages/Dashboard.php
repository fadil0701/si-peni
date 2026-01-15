<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\DistributionFlowWidget;
use App\Filament\Widgets\RequestStatusChartWidget;
use App\Filament\Widgets\LatestAssetsWidget;
use App\Filament\Widgets\TransactionHistoryWidget;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = null;
    
    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            DistributionFlowWidget::class,
            RequestStatusChartWidget::class,
            LatestAssetsWidget::class,
            TransactionHistoryWidget::class,
        ];
    }
}

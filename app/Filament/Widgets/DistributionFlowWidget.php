<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DistributionFlowWidget extends Widget
{
    protected string $view = 'filament.widgets.distribution-flow-widget';

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    
    protected static ?int $sort = 2;
}

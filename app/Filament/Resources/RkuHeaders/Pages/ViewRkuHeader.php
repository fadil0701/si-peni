<?php

namespace App\Filament\Resources\RkuHeaders\Pages;

use App\Filament\Resources\RkuHeaders\RkuHeaderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRkuHeader extends ViewRecord
{
    protected static string $resource = RkuHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

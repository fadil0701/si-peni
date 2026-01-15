<?php

namespace App\Filament\Resources\DataInventories\Pages;

use App\Filament\Resources\DataInventories\DataInventoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDataInventory extends ViewRecord
{
    protected static string $resource = DataInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

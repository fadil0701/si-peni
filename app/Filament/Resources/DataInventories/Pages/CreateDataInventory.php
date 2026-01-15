<?php

namespace App\Filament\Resources\DataInventories\Pages;

use App\Filament\Resources\DataInventories\DataInventoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDataInventory extends CreateRecord
{
    protected static string $resource = DataInventoryResource::class;
    
    public function getView(): string
    {
        return 'filament.resources.data-inventories.pages.create-data-inventory';
    }
}

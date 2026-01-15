<?php

namespace App\Filament\Resources\DataInventories\Pages;

use App\Filament\Resources\DataInventories\DataInventoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDataInventory extends EditRecord
{
    protected static string $resource = DataInventoryResource::class;
    
    public function getView(): string
    {
        return 'filament.resources.data-inventories.pages.edit-data-inventory';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

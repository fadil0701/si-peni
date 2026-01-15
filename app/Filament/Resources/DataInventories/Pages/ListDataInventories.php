<?php

namespace App\Filament\Resources\DataInventories\Pages;

use App\Filament\Resources\DataInventories\DataInventoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDataInventories extends ListRecords
{
    protected static string $resource = DataInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

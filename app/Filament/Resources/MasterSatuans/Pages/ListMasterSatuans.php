<?php

namespace App\Filament\Resources\MasterSatuans\Pages;

use App\Filament\Resources\MasterSatuans\MasterSatuanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterSatuans extends ListRecords
{
    protected static string $resource = MasterSatuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

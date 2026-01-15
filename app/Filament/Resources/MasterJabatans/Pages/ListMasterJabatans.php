<?php

namespace App\Filament\Resources\MasterJabatans\Pages;

use App\Filament\Resources\MasterJabatans\MasterJabatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterJabatans extends ListRecords
{
    protected static string $resource = MasterJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

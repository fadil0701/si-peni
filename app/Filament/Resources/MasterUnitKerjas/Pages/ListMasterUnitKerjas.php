<?php

namespace App\Filament\Resources\MasterUnitKerjas\Pages;

use App\Filament\Resources\MasterUnitKerjas\MasterUnitKerjaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterUnitKerjas extends ListRecords
{
    protected static string $resource = MasterUnitKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

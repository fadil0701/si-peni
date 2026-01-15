<?php

namespace App\Filament\Resources\MasterPegawais\Pages;

use App\Filament\Resources\MasterPegawais\MasterPegawaiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterPegawais extends ListRecords
{
    protected static string $resource = MasterPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

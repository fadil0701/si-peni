<?php

namespace App\Filament\Resources\MasterPrograms\Pages;

use App\Filament\Resources\MasterPrograms\MasterProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterPrograms extends ListRecords
{
    protected static string $resource = MasterProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

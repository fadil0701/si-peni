<?php

namespace App\Filament\Resources\MasterUnitKerjas\Pages;

use App\Filament\Resources\MasterUnitKerjas\MasterUnitKerjaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterUnitKerja extends EditRecord
{
    protected static string $resource = MasterUnitKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

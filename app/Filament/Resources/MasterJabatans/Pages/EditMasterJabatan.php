<?php

namespace App\Filament\Resources\MasterJabatans\Pages;

use App\Filament\Resources\MasterJabatans\MasterJabatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterJabatan extends EditRecord
{
    protected static string $resource = MasterJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

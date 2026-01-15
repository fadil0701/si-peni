<?php

namespace App\Filament\Resources\MasterSatuans\Pages;

use App\Filament\Resources\MasterSatuans\MasterSatuanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterSatuan extends EditRecord
{
    protected static string $resource = MasterSatuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

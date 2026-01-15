<?php

namespace App\Filament\Resources\MasterGudangs\Pages;

use App\Filament\Resources\MasterGudangs\MasterGudangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterGudang extends EditRecord
{
    protected static string $resource = MasterGudangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

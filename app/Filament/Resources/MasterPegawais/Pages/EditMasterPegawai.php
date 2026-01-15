<?php

namespace App\Filament\Resources\MasterPegawais\Pages;

use App\Filament\Resources\MasterPegawais\MasterPegawaiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterPegawai extends EditRecord
{
    protected static string $resource = MasterPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

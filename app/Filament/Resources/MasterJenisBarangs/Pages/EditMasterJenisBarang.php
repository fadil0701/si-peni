<?php

namespace App\Filament\Resources\MasterJenisBarangs\Pages;

use App\Filament\Resources\MasterJenisBarangs\MasterJenisBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterJenisBarang extends EditRecord
{
    protected static string $resource = MasterJenisBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

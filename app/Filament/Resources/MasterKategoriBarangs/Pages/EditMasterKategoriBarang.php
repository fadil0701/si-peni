<?php

namespace App\Filament\Resources\MasterKategoriBarangs\Pages;

use App\Filament\Resources\MasterKategoriBarangs\MasterKategoriBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterKategoriBarang extends EditRecord
{
    protected static string $resource = MasterKategoriBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

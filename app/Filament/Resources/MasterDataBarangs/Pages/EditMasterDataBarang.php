<?php

namespace App\Filament\Resources\MasterDataBarangs\Pages;

use App\Filament\Resources\MasterDataBarangs\MasterDataBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterDataBarang extends EditRecord
{
    protected static string $resource = MasterDataBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

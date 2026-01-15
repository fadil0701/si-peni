<?php

namespace App\Filament\Resources\MasterSubjenisBarangs\Pages;

use App\Filament\Resources\MasterSubjenisBarangs\MasterSubjenisBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterSubjenisBarang extends EditRecord
{
    protected static string $resource = MasterSubjenisBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

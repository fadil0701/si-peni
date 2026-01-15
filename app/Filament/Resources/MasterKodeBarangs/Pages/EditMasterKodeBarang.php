<?php

namespace App\Filament\Resources\MasterKodeBarangs\Pages;

use App\Filament\Resources\MasterKodeBarangs\MasterKodeBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterKodeBarang extends EditRecord
{
    protected static string $resource = MasterKodeBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

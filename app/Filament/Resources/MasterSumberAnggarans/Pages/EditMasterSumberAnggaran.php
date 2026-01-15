<?php

namespace App\Filament\Resources\MasterSumberAnggarans\Pages;

use App\Filament\Resources\MasterSumberAnggarans\MasterSumberAnggaranResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterSumberAnggaran extends EditRecord
{
    protected static string $resource = MasterSumberAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

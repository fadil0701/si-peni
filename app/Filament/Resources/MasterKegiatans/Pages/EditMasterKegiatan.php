<?php

namespace App\Filament\Resources\MasterKegiatans\Pages;

use App\Filament\Resources\MasterKegiatans\MasterKegiatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterKegiatan extends EditRecord
{
    protected static string $resource = MasterKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

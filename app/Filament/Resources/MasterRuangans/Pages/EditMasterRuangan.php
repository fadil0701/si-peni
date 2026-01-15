<?php

namespace App\Filament\Resources\MasterRuangans\Pages;

use App\Filament\Resources\MasterRuangans\MasterRuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterRuangan extends EditRecord
{
    protected static string $resource = MasterRuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\MasterAsets\Pages;

use App\Filament\Resources\MasterAsets\MasterAsetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterAset extends EditRecord
{
    protected static string $resource = MasterAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

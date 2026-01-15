<?php

namespace App\Filament\Resources\MasterPrograms\Pages;

use App\Filament\Resources\MasterPrograms\MasterProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterProgram extends EditRecord
{
    protected static string $resource = MasterProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

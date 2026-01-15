<?php

namespace App\Filament\Resources\RegisterAsets\Pages;

use App\Filament\Resources\RegisterAsets\RegisterAsetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRegisterAset extends EditRecord
{
    protected static string $resource = RegisterAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

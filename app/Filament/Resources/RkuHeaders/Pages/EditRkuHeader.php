<?php

namespace App\Filament\Resources\RkuHeaders\Pages;

use App\Filament\Resources\RkuHeaders\RkuHeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRkuHeader extends EditRecord
{
    protected static string $resource = RkuHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

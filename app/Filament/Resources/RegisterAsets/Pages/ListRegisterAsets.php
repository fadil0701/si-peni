<?php

namespace App\Filament\Resources\RegisterAsets\Pages;

use App\Filament\Resources\RegisterAsets\RegisterAsetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegisterAsets extends ListRecords
{
    protected static string $resource = RegisterAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

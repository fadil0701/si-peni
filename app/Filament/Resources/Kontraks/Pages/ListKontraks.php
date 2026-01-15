<?php

namespace App\Filament\Resources\Kontraks\Pages;

use App\Filament\Resources\Kontraks\KontrakResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKontraks extends ListRecords
{
    protected static string $resource = KontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\MasterKegiatans\Pages;

use App\Filament\Resources\MasterKegiatans\MasterKegiatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterKegiatans extends ListRecords
{
    protected static string $resource = MasterKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

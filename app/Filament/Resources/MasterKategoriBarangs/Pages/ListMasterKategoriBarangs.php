<?php

namespace App\Filament\Resources\MasterKategoriBarangs\Pages;

use App\Filament\Resources\MasterKategoriBarangs\MasterKategoriBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterKategoriBarangs extends ListRecords
{
    protected static string $resource = MasterKategoriBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

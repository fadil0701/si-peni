<?php

namespace App\Filament\Resources\MasterKodeBarangs\Pages;

use App\Filament\Resources\MasterKodeBarangs\MasterKodeBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterKodeBarangs extends ListRecords
{
    protected static string $resource = MasterKodeBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

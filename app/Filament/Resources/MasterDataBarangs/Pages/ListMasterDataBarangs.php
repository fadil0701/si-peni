<?php

namespace App\Filament\Resources\MasterDataBarangs\Pages;

use App\Filament\Resources\MasterDataBarangs\MasterDataBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterDataBarangs extends ListRecords
{
    protected static string $resource = MasterDataBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

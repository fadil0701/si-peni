<?php

namespace App\Filament\Resources\MasterSubjenisBarangs\Pages;

use App\Filament\Resources\MasterSubjenisBarangs\MasterSubjenisBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterSubjenisBarangs extends ListRecords
{
    protected static string $resource = MasterSubjenisBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

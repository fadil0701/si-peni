<?php

namespace App\Filament\Resources\MasterGudangs\Pages;

use App\Filament\Resources\MasterGudangs\MasterGudangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterGudangs extends ListRecords
{
    protected static string $resource = MasterGudangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

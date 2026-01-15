<?php

namespace App\Filament\Resources\MasterSumberAnggarans\Pages;

use App\Filament\Resources\MasterSumberAnggarans\MasterSumberAnggaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterSumberAnggarans extends ListRecords
{
    protected static string $resource = MasterSumberAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

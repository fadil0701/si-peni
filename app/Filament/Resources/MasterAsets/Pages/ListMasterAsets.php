<?php

namespace App\Filament\Resources\MasterAsets\Pages;

use App\Filament\Resources\MasterAsets\MasterAsetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterAsets extends ListRecords
{
    protected static string $resource = MasterAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

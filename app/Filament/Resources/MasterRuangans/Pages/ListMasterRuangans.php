<?php

namespace App\Filament\Resources\MasterRuangans\Pages;

use App\Filament\Resources\MasterRuangans\MasterRuanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterRuangans extends ListRecords
{
    protected static string $resource = MasterRuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

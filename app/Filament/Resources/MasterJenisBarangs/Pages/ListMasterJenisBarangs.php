<?php

namespace App\Filament\Resources\MasterJenisBarangs\Pages;

use App\Filament\Resources\MasterJenisBarangs\MasterJenisBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterJenisBarangs extends ListRecords
{
    protected static string $resource = MasterJenisBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

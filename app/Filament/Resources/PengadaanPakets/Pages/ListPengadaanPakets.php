<?php

namespace App\Filament\Resources\PengadaanPakets\Pages;

use App\Filament\Resources\PengadaanPakets\PengadaanPaketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengadaanPakets extends ListRecords
{
    protected static string $resource = PengadaanPaketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

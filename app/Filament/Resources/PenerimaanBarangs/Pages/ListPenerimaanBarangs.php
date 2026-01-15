<?php

namespace App\Filament\Resources\PenerimaanBarangs\Pages;

use App\Filament\Resources\PenerimaanBarangs\PenerimaanBarangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenerimaanBarangs extends ListRecords
{
    protected static string $resource = PenerimaanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

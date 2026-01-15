<?php

namespace App\Filament\Resources\PermintaanBarangs\Pages;

use App\Filament\Resources\PermintaanBarangs\PermintaanBarangResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanBarang extends ViewRecord
{
    protected static string $resource = PermintaanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

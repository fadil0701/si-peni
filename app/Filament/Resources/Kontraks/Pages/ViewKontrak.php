<?php

namespace App\Filament\Resources\Kontraks\Pages;

use App\Filament\Resources\Kontraks\KontrakResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKontrak extends ViewRecord
{
    protected static string $resource = KontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

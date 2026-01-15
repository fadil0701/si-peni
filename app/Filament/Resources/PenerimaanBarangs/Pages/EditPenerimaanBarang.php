<?php

namespace App\Filament\Resources\PenerimaanBarangs\Pages;

use App\Filament\Resources\PenerimaanBarangs\PenerimaanBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenerimaanBarang extends EditRecord
{
    protected static string $resource = PenerimaanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

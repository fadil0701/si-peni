<?php

namespace App\Filament\Resources\PengadaanPakets\Pages;

use App\Filament\Resources\PengadaanPakets\PengadaanPaketResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPengadaanPaket extends EditRecord
{
    protected static string $resource = PengadaanPaketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

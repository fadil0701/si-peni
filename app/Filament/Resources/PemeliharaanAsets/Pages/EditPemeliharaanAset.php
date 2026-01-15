<?php

namespace App\Filament\Resources\PemeliharaanAsets\Pages;

use App\Filament\Resources\PemeliharaanAsets\PemeliharaanAsetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemeliharaanAset extends EditRecord
{
    protected static string $resource = PemeliharaanAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

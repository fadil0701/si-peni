<?php

namespace App\Filament\Resources\PemeliharaanAsets\Pages;

use App\Filament\Resources\PemeliharaanAsets\PemeliharaanAsetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeliharaanAsets extends ListRecords
{
    protected static string $resource = PemeliharaanAsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

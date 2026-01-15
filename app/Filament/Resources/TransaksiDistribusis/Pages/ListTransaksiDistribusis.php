<?php

namespace App\Filament\Resources\TransaksiDistribusis\Pages;

use App\Filament\Resources\TransaksiDistribusis\TransaksiDistribusiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiDistribusis extends ListRecords
{
    protected static string $resource = TransaksiDistribusiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

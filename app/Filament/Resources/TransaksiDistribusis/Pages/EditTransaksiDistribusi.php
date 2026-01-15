<?php

namespace App\Filament\Resources\TransaksiDistribusis\Pages;

use App\Filament\Resources\TransaksiDistribusis\TransaksiDistribusiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiDistribusi extends EditRecord
{
    protected static string $resource = TransaksiDistribusiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

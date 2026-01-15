<?php

namespace App\Filament\Resources\RkuHeaders\Pages;

use App\Filament\Resources\RkuHeaders\RkuHeaderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRkuHeaders extends ListRecords
{
    protected static string $resource = RkuHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

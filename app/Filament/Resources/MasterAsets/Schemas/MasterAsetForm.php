<?php

namespace App\Filament\Resources\MasterAsets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterAsetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_aset')
                    ->label('Nama Aset')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}

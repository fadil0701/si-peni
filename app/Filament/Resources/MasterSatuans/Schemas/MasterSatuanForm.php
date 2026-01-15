<?php

namespace App\Filament\Resources\MasterSatuans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterSatuanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_satuan')
                    ->label('Nama Satuan')
                    ->required()
                    ->maxLength(50),
            ]);
    }
}

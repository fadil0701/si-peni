<?php

namespace App\Filament\Resources\MasterJabatans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterJabatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_jabatan')
                    ->label('Nama Jabatan')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}

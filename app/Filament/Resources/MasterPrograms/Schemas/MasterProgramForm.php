<?php

namespace App\Filament\Resources\MasterPrograms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_program')
                    ->label('Nama Program')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}

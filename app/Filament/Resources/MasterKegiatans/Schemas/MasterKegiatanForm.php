<?php

namespace App\Filament\Resources\MasterKegiatans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterKegiatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_program')
                    ->label('Program')
                    ->relationship('program', 'nama_program')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                TextInput::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}

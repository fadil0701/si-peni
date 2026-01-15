<?php

namespace App\Filament\Resources\MasterRuangans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class MasterRuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('id_unit_kerja')
                        ->label('Unit Kerja')
                        ->relationship('unitKerja', 'nama_unit_kerja')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive(),
                    
                    TextInput::make('kode_ruangan')
                        ->label('Kode Ruangan')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    
                    TextInput::make('nama_ruangan')
                        ->label('Nama Ruangan')
                        ->required()
                        ->maxLength(255),
                ])->columns(3),
            ]);
    }
}

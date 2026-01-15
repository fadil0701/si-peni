<?php

namespace App\Filament\Resources\MasterSubjenisBarangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class MasterSubjenisBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('id_jenis_barang')
                        ->label('Jenis Barang')
                        ->relationship('jenisBarang', 'nama_jenis_barang')
                        ->searchable()
                        ->preload()
                        ->required(),
                    
                    TextInput::make('kode_subjenis_barang')
                        ->label('Kode Sub Jenis')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    
                    TextInput::make('nama_subjenis_barang')
                        ->label('Nama Sub Jenis Barang')
                        ->required()
                        ->maxLength(255),
                ])->columns(3),
            ]);
    }
}

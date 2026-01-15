<?php

namespace App\Filament\Resources\MasterJenisBarangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class MasterJenisBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('id_kategori_barang')
                        ->label('Kategori Barang')
                        ->relationship('kategoriBarang', 'nama_kategori_barang')
                        ->searchable()
                        ->preload()
                        ->required(),
                    
                    TextInput::make('kode_jenis_barang')
                        ->label('Kode Jenis')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    
                    TextInput::make('nama_jenis_barang')
                        ->label('Nama Jenis Barang')
                        ->required()
                        ->maxLength(255),
                ])->columns(3),
            ]);
    }
}

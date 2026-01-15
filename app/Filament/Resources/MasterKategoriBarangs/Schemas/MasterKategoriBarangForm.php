<?php

namespace App\Filament\Resources\MasterKategoriBarangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class MasterKategoriBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('id_kode_barang')
                        ->label('Kode Barang')
                        ->relationship('kodeBarang', 'nama_kode_barang')
                        ->searchable()
                        ->preload()
                        ->required(),
                    
                    TextInput::make('kode_kategori_barang')
                        ->label('Kode Kategori')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    
                    TextInput::make('nama_kategori_barang')
                        ->label('Nama Kategori Barang')
                        ->required()
                        ->maxLength(255),
                ])->columns(3),
            ]);
    }
}

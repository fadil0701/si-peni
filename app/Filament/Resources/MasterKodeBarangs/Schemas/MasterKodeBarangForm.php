<?php

namespace App\Filament\Resources\MasterKodeBarangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class MasterKodeBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('id_aset')
                        ->label('Aset')
                        ->relationship('aset', 'nama_aset')
                        ->searchable()
                        ->preload()
                        ->required(),
                    
                    TextInput::make('kode_barang')
                        ->label('Kode Barang')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    
                    TextInput::make('nama_kode_barang')
                        ->label('Nama Kode Barang')
                        ->required()
                        ->maxLength(255),
                ])->columns(3),
            ]);
    }
}

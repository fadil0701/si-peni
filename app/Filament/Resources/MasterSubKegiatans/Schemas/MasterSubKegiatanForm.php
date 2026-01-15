<?php

namespace App\Filament\Resources\MasterSubKegiatans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MasterSubKegiatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_kegiatan')
                    ->label('Kegiatan')
                    ->relationship('kegiatan', 'nama_kegiatan')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Reset kode saat kegiatan berubah
                        $set('kode_sub_kegiatan', null);
                    }),
                
                TextInput::make('kode_sub_kegiatan')
                    ->label('Kode Sub Kegiatan')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                
                TextInput::make('nama_sub_kegiatan')
                    ->label('Nama Sub Kegiatan')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}

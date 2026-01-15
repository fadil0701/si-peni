<?php

namespace App\Filament\Resources\MasterPegawais\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MasterPegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pegawai')
                    ->schema([
                        Group::make([
                            TextInput::make('nip_pegawai')
                                ->label('NIP')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true),
                            
                            TextInput::make('nama_pegawai')
                                ->label('Nama Pegawai')
                                ->required()
                                ->maxLength(255),
                        ])->columns(2),
                        
                        Group::make([
                            Select::make('id_unit_kerja')
                                ->label('Unit Kerja')
                                ->relationship('unitKerja', 'nama_unit_kerja')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            Select::make('id_jabatan')
                                ->label('Jabatan')
                                ->relationship('jabatan', 'nama_jabatan')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])->columns(2),
                        
                        Group::make([
                            TextInput::make('email_pegawai')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),
                            
                            TextInput::make('no_telp')
                                ->label('No Telepon')
                                ->tel()
                                ->maxLength(20),
                        ])->columns(2),
                    ]),
            ]);
    }
}

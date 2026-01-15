<?php

namespace App\Filament\Resources\MasterDataBarangs\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MasterDataBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->schema([
                        Group::make([
                            Select::make('id_subjenis_barang')
                                ->label('Sub Jenis Barang')
                                ->relationship('subjenisBarang', 'nama_subjenis_barang')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            Select::make('id_satuan')
                                ->label('Satuan')
                                ->relationship('satuan', 'nama_satuan')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            TextInput::make('kode_data_barang')
                                ->label('Kode Data Barang')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true),
                        ])->columns(3),
                        
                        TextInput::make('nama_barang')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Informasi Teknis')
                    ->schema([
                        Group::make([
                            TextInput::make('merk')
                                ->label('Merk')
                                ->maxLength(100),
                            
                            TextInput::make('tipe')
                                ->label('Tipe')
                                ->maxLength(100),
                            
                            TextInput::make('tahun_produksi')
                                ->label('Tahun Produksi')
                                ->numeric()
                                ->maxLength(4),
                        ])->columns(3),
                        
                        Textarea::make('spesifikasi')
                            ->label('Spesifikasi')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        FileUpload::make('foto_barang')
                            ->label('Foto Barang')
                            ->image()
                            ->directory('barang/foto')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}

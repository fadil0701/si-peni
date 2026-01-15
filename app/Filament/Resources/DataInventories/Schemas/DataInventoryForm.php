<?php

namespace App\Filament\Resources\DataInventories\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class DataInventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Form Data Inventory')
                    ->schema([
                        Group::make([
                            Select::make('id_data_barang')
                                ->label('Data Barang')
                                ->relationship('dataBarang', 'nama_barang')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Auto fill merk, tipe, spesifikasi dari data barang jika ada
                                    if ($state) {
                                        $dataBarang = \App\Models\MasterDataBarang::find($state);
                                        if ($dataBarang) {
                                            if ($dataBarang->merk) $set('merk', $dataBarang->merk);
                                            if ($dataBarang->tipe) $set('tipe', $dataBarang->tipe);
                                            if ($dataBarang->spesifikasi) $set('spesifikasi', $dataBarang->spesifikasi);
                                        }
                                    }
                                }),
                            
                            Select::make('id_gudang')
                                ->label('Gudang')
                                ->relationship('gudang', 'nama_gudang')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            Select::make('id_anggaran')
                                ->label('Sumber Anggaran')
                                ->relationship('sumberAnggaran', 'nama_anggaran')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])->columns(3),
                        
                        Group::make([
                            Select::make('id_sub_kegiatan')
                                ->label('Sub Kegiatan')
                                ->relationship('subKegiatan', 'nama_sub_kegiatan')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            TextInput::make('tahun_anggaran')
                                ->label('Tahun Anggaran')
                                ->numeric()
                                ->required()
                                ->default(now()->year)
                                ->maxLength(4),
                            
                            Select::make('id_satuan')
                                ->label('Satuan')
                                ->relationship('satuan', 'nama_satuan')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])->columns(3),
                        
                        // Radio Button untuk Jenis Inventory
                        Radio::make('jenis_inventory')
                            ->label('Jenis Inventory')
                            ->options([
                                'ASET' => 'ASET',
                                'PERSEDIAAN' => 'PERSEDIAAN',
                                'FARMASI' => 'FARMASI',
                            ])
                            ->required()
                            ->default('ASET')
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Reset fields berdasarkan jenis
                                if ($state === 'ASET') {
                                    $set('no_batch', null);
                                    $set('tanggal_kedaluwarsa', null);
                                } else {
                                    $set('no_seri', null);
                                }
                            }),
                    ]),
                
                Section::make('Kuantitas & Harga')
                    ->schema([
                        Group::make([
                            TextInput::make('qty_input')
                                ->label('Jumlah Barang')
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->minValue(0)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    // Auto calculate total_harga
                                    $hargaSatuan = floatval($get('harga_satuan') ?? 0);
                                    $qty = floatval($state ?? 0);
                                    $set('total_harga', $hargaSatuan * $qty);
                                }),
                            
                            TextInput::make('harga_satuan')
                                ->label('Harga Satuan')
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->prefix('Rp')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    // Auto calculate total_harga
                                    $qty = floatval($get('qty_input') ?? 0);
                                    $hargaSatuan = floatval($state ?? 0);
                                    $set('total_harga', $hargaSatuan * $qty);
                                }),
                            
                            TextInput::make('total_harga')
                                ->label('Total Harga')
                                ->numeric()
                                ->required()
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated(),
                        ])->columns(3),
                    ]),
                
                Section::make('Informasi Teknis')
                    ->schema([
                        Group::make([
                            TextInput::make('merk')
                                ->label('Merk')
                                ->maxLength(100),
                            
                            TextInput::make('tipe')
                                ->label('Type')
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
                    ])
                    ->collapsible(),
                
                Section::make('Informasi Batch / Seri')
                    ->schema([
                        Group::make([
                            TextInput::make('no_seri')
                                ->label('Nomor Seri')
                                ->maxLength(255)
                                ->visible(fn ($get) => $get('jenis_inventory') === 'ASET')
                                ->helperText('Hanya untuk ASET'),
                            
                            TextInput::make('no_batch')
                                ->label('Nomor Batch')
                                ->maxLength(255)
                                ->visible(fn ($get) => in_array($get('jenis_inventory'), ['PERSEDIAAN', 'FARMASI']))
                                ->helperText('Untuk PERSEDIAAN dan FARMASI'),
                            
                            DatePicker::make('tanggal_kedaluwarsa')
                                ->label('Tanggal Kedaluwarsa')
                                ->displayFormat('d/m/Y')
                                ->visible(fn ($get) => in_array($get('jenis_inventory'), ['PERSEDIAAN', 'FARMASI']))
                                ->helperText('Untuk PERSEDIAAN dan FARMASI'),
                        ])->columns(3),
                    ])
                    ->collapsible(),
                
                Section::make('Status & Dokumen')
                    ->schema([
                        Group::make([
                            Select::make('status_inventory')
                                ->label('Status Inventory')
                                ->options([
                                    'DRAFT' => 'DRAFT',
                                    'AKTIF' => 'AKTIF',
                                    'DISTRIBUSI' => 'DISTRIBUSI',
                                    'HABIS' => 'HABIS',
                                ])
                                ->default('DRAFT')
                                ->required(),
                            
                            FileUpload::make('upload_dokumen')
                                ->label('Dokumen Penerimaan')
                                ->directory('inventory/dokumen')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->maxSize(5120)
                                ->helperText('BA / Faktur / SP')
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->collapsible(),
                
                // Hidden field untuk created_by
                TextInput::make('created_by')
                    ->default(fn () => Auth::id())
                    ->hidden()
                    ->required(),
            ]);
    }
}

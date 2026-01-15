<?php

namespace App\Filament\Resources\Kontraks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KontrakForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kontrak')
                    ->schema([
                        Group::make([
                            Select::make('id_paket')
                                ->label('Paket Pengadaan')
                                ->relationship('paket', 'nama_paket', fn ($query) => 
                                    $query->whereIn('status_paket', ['DIAJUKAN', 'DIPROSES', 'SELESAI'])
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $paket = \App\Models\PengadaanPaket::find($state);
                                        if ($paket) {
                                            $set('nilai_kontrak', $paket->nilai_paket);
                                        }
                                    }
                                }),
                            
                            TextInput::make('no_kontrak')
                                ->label('No. Kontrak')
                                ->required()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => 'KONTRAK-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)),
                        ])->columns(2),
                        
                        Group::make([
                            TextInput::make('no_sp')
                                ->label('No. Surat Pesanan (SP)')
                                ->maxLength(100)
                                ->unique(ignoreRecord: true),
                            
                            TextInput::make('no_po')
                                ->label('No. Purchase Order (PO)')
                                ->maxLength(100)
                                ->unique(ignoreRecord: true),
                        ])->columns(2),
                    ]),
                
                Section::make('Informasi Vendor')
                    ->schema([
                        Group::make([
                            TextInput::make('nama_vendor')
                                ->label('Nama Vendor')
                                ->required()
                                ->maxLength(255),
                            
                            TextInput::make('npwp_vendor')
                                ->label('NPWP Vendor')
                                ->maxLength(50),
                        ])->columns(2),
                        
                        Textarea::make('alamat_vendor')
                            ->label('Alamat Vendor')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Detail Kontrak')
                    ->schema([
                        Group::make([
                            TextInput::make('nilai_kontrak')
                                ->label('Nilai Kontrak')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->default(0),
                            
                            DatePicker::make('tanggal_kontrak')
                                ->label('Tanggal Kontrak')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                            
                            DatePicker::make('tanggal_mulai')
                                ->label('Tanggal Mulai')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                            
                            DatePicker::make('tanggal_selesai')
                                ->label('Tanggal Selesai')
                                ->required()
                                ->displayFormat('d/m/Y')
                                ->after('tanggal_mulai'),
                        ])->columns(4),
                        
                        Group::make([
                            Select::make('jenis_pembayaran')
                                ->label('Jenis Pembayaran')
                                ->options([
                                    'TUNAI' => 'TUNAI',
                                    'UANG_MUKA' => 'UANG MUKA',
                                    'TERMIN' => 'TERMIN',
                                    'PELUNASAN' => 'PELUNASAN',
                                ])
                                ->required()
                                ->default('TERMIN')
                                ->reactive(),
                            
                            TextInput::make('jumlah_termin')
                                ->label('Jumlah Termin')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1)
                                ->visible(fn ($get) => in_array($get('jenis_pembayaran'), ['TERMIN', 'UANG_MUKA'])),
                            
                            Select::make('status_kontrak')
                                ->label('Status Kontrak')
                                ->options([
                                    'DRAFT' => 'DRAFT',
                                    'AKTIF' => 'AKTIF',
                                    'SELESAI' => 'SELESAI',
                                    'DIBATALKAN' => 'DIBATALKAN',
                                ])
                                ->required()
                                ->default('DRAFT'),
                        ])->columns(3),
                    ]),
                
                Section::make('Dokumen')
                    ->schema([
                        FileUpload::make('upload_dokumen')
                            ->label('Upload Dokumen Kontrak')
                            ->directory('kontrak')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

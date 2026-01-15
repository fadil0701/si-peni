<?php

namespace App\Filament\Resources\PengadaanPakets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengadaanPaketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Paket')
                    ->schema([
                        Group::make([
                            Select::make('id_sub_kegiatan')
                                ->label('Sub Kegiatan')
                                ->relationship('subKegiatan', 'nama_sub_kegiatan')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive(),
                            
                            Select::make('id_rku')
                                ->label('RKU')
                                ->relationship('rku', 'no_rku', fn ($query) => 
                                    $query->where('status_rku', 'DISETUJUI')
                                )
                                ->searchable()
                                ->preload()
                                ->helperText('Pilih RKU yang sudah disetujui'),
                        ])->columns(2),
                        
                        Group::make([
                            TextInput::make('no_paket')
                                ->label('No. Paket')
                                ->required()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => 'PAKET-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)),
                            
                            TextInput::make('nama_paket')
                                ->label('Nama Paket')
                                ->required()
                                ->maxLength(255),
                        ])->columns(2),
                        
                        Textarea::make('deskripsi_paket')
                            ->label('Deskripsi Paket')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Detail Pengadaan')
                    ->schema([
                        Group::make([
                            Select::make('metode_pengadaan')
                                ->label('Metode Pengadaan')
                                ->options([
                                    'PEMILIHAN_LANGSUNG' => 'Pemilihan Langsung',
                                    'PENUNJUKAN_LANGSUNG' => 'Penunjukan Langsung',
                                    'TENDER' => 'Tender',
                                    'SWAKELOLA' => 'Swakelola',
                                ])
                                ->required()
                                ->default('PEMILIHAN_LANGSUNG'),
                            
                            TextInput::make('nilai_paket')
                                ->label('Nilai Paket')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->default(0),
                            
                            DatePicker::make('tanggal_mulai')
                                ->label('Tanggal Mulai')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                            
                            DatePicker::make('tanggal_selesai')
                                ->label('Tanggal Selesai')
                                ->displayFormat('d/m/Y')
                                ->after('tanggal_mulai'),
                        ])->columns(4),
                        
                        Group::make([
                            Select::make('status_paket')
                                ->label('Status Paket')
                                ->options([
                                    'DRAFT' => 'DRAFT',
                                    'DIAJUKAN' => 'DIAJUKAN',
                                    'DIPROSES' => 'DIPROSES',
                                    'SELESAI' => 'SELESAI',
                                    'DIBATALKAN' => 'DIBATALKAN',
                                ])
                                ->required()
                                ->default('DRAFT'),
                            
                            Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembayaran')
                    ->schema([
                        Group::make([
                            Select::make('id_kontrak')
                                ->label('Kontrak')
                                ->relationship('kontrak', 'no_kontrak', fn ($query) => 
                                    $query->where('status_kontrak', 'AKTIF')
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $kontrak = \App\Models\Kontrak::find($state);
                                        if ($kontrak) {
                                            // Set default nilai berdasarkan kontrak
                                            $set('nilai_pembayaran', $kontrak->nilai_kontrak / max($kontrak->jumlah_termin, 1));
                                        }
                                    }
                                }),
                            
                            TextInput::make('no_pembayaran')
                                ->label('No. Pembayaran')
                                ->required()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => 'PAY-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)),
                        ])->columns(2),
                        
                        Group::make([
                            Select::make('jenis_pembayaran')
                                ->label('Jenis Pembayaran')
                                ->options([
                                    'UANG_MUKA' => 'UANG MUKA',
                                    'TERMIN' => 'TERMIN',
                                    'PELUNASAN' => 'PELUNASAN',
                                ])
                                ->required()
                                ->default('TERMIN')
                                ->reactive(),
                            
                            TextInput::make('termin_ke')
                                ->label('Termin Ke')
                                ->numeric()
                                ->minValue(1)
                                ->visible(fn ($get) => $get('jenis_pembayaran') === 'TERMIN')
                                ->required(fn ($get) => $get('jenis_pembayaran') === 'TERMIN'),
                        ])->columns(2),
                    ]),
                
                Section::make('Detail Pembayaran')
                    ->schema([
                        Group::make([
                            TextInput::make('nilai_pembayaran')
                                ->label('Nilai Pembayaran')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $nilai = floatval($state ?? 0);
                                    $ppn = floatval($get('ppn') ?? 0);
                                    $pph = floatval($get('pph') ?? 0);
                                    $set('total_pembayaran', $nilai + $ppn - $pph);
                                }),
                            
                            TextInput::make('ppn')
                                ->label('PPN')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $nilai = floatval($get('nilai_pembayaran') ?? 0);
                                    $ppn = floatval($state ?? 0);
                                    $pph = floatval($get('pph') ?? 0);
                                    $set('total_pembayaran', $nilai + $ppn - $pph);
                                }),
                            
                            TextInput::make('pph')
                                ->label('PPh')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $nilai = floatval($get('nilai_pembayaran') ?? 0);
                                    $ppn = floatval($get('ppn') ?? 0);
                                    $pph = floatval($state ?? 0);
                                    $set('total_pembayaran', $nilai + $ppn - $pph);
                                }),
                            
                            TextInput::make('total_pembayaran')
                                ->label('Total Pembayaran')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated(),
                        ])->columns(4),
                        
                        Group::make([
                            DatePicker::make('tanggal_pembayaran')
                                ->label('Tanggal Pembayaran')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                            
                            Select::make('status_pembayaran')
                                ->label('Status Pembayaran')
                                ->options([
                                    'DRAFT' => 'DRAFT',
                                    'DIAJUKAN' => 'DIAJUKAN',
                                    'DIVERIFIKASI' => 'DIVERIFIKASI',
                                    'DIBAYAR' => 'DIBAYAR',
                                    'DITOLAK' => 'DITOLAK',
                                ])
                                ->required()
                                ->default('DRAFT'),
                        ])->columns(2),
                    ]),
                
                Section::make('Verifikasi')
                    ->schema([
                        Group::make([
                            Select::make('id_verifikator')
                                ->label('Verifikator')
                                ->relationship('verifikator', 'nama_pegawai')
                                ->searchable()
                                ->preload()
                                ->disabled(fn ($get) => $get('status_pembayaran') !== 'DIAJUKAN'),
                            
                            DatePicker::make('tanggal_verifikasi')
                                ->label('Tanggal Verifikasi')
                                ->displayFormat('d/m/Y')
                                ->disabled(fn ($get) => !$get('id_verifikator')),
                        ])->columns(2),
                        
                        Textarea::make('catatan_verifikasi')
                            ->label('Catatan Verifikasi')
                            ->rows(2)
                            ->columnSpanFull()
                            ->disabled(fn ($get) => !$get('id_verifikator')),
                    ]),
                
                Section::make('Bukti Pembayaran')
                    ->schema([
                        Group::make([
                            TextInput::make('no_bukti_bayar')
                                ->label('No. Bukti Bayar')
                                ->maxLength(100),
                            
                            FileUpload::make('upload_bukti_bayar')
                                ->label('Upload Bukti Bayar')
                                ->directory('pembayaran')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->maxSize(5120)
                                ->columnSpanFull(),
                            
                            Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}

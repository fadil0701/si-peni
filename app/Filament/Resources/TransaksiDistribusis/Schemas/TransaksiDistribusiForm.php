<?php

namespace App\Filament\Resources\TransaksiDistribusis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransaksiDistribusiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Distribusi')
                    ->schema([
                        Group::make([
                            TextInput::make('no_sbbk')
                                ->label('No SBBK')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => 'SBBK-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)),
                            
                            Select::make('id_permintaan')
                                ->label('Permintaan Barang')
                                ->relationship('permintaan', 'no_permintaan')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            DatePicker::make('tanggal_distribusi')
                                ->label('Tanggal Distribusi')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                        ])->columns(3),
                        
                        Group::make([
                            Select::make('id_gudang_asal')
                                ->label('Gudang Asal')
                                ->relationship('gudangAsal', 'nama_gudang')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            Select::make('id_gudang_tujuan')
                                ->label('Gudang Tujuan')
                                ->relationship('gudangTujuan', 'nama_gudang')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            Select::make('id_pegawai_pengirim')
                                ->label('Pegawai Pengirim')
                                ->relationship('pegawaiPengirim', 'nama_pegawai')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])->columns(3),
                        
                        Group::make([
                            Select::make('status_distribusi')
                                ->label('Status Distribusi')
                                ->options([
                                    'DRAFT' => 'DRAFT',
                                    'DIPROSES' => 'DIPROSES',
                                    'TERKIRIM' => 'TERKIRIM',
                                    'SELESAI' => 'SELESAI',
                                    'BATAL' => 'BATAL',
                                ])
                                ->default('DRAFT')
                                ->required(),
                            
                            Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}

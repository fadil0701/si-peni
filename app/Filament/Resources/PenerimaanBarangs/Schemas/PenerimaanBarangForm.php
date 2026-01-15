<?php

namespace App\Filament\Resources\PenerimaanBarangs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PenerimaanBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penerimaan')
                    ->schema([
                        Group::make([
                            TextInput::make('no_penerimaan')
                                ->label('No Penerimaan')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => 'TERIMA-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)),
                            
                            Select::make('id_distribusi')
                                ->label('Distribusi Barang')
                                ->relationship('distribusi', 'no_sbbk')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Auto fill unit kerja dari distribusi
                                    if ($state) {
                                        $distribusi = \App\Models\TransaksiDistribusi::find($state);
                                        if ($distribusi && $distribusi->permintaan) {
                                            $set('id_unit_kerja', $distribusi->permintaan->id_unit_kerja);
                                        }
                                    }
                                }),
                            
                            DatePicker::make('tanggal_penerimaan')
                                ->label('Tanggal Penerimaan')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                        ])->columns(3),
                        
                        Group::make([
                            Select::make('id_unit_kerja')
                                ->label('Unit Kerja')
                                ->relationship('unitKerja', 'nama_unit_kerja')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Reset pegawai penerima saat unit kerja berubah
                                    $set('id_pegawai_penerima', null);
                                }),
                            
                            Select::make('id_pegawai_penerima')
                                ->label('Pegawai Penerima')
                                ->relationship('pegawaiPenerima', 'nama_pegawai', fn ($query, $get) => 
                                    $query->where('id_unit_kerja', $get('id_unit_kerja'))
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->disabled(fn ($get) => !$get('id_unit_kerja'))
                                ->helperText('Pilih unit kerja terlebih dahulu'),
                            
                            Select::make('status_penerimaan')
                                ->label('Status Penerimaan')
                                ->options([
                                    'DRAFT' => 'DRAFT',
                                    'DITERIMA' => 'DITERIMA',
                                    'DITOLAK' => 'DITOLAK',
                                    'SELESAI' => 'SELESAI',
                                ])
                                ->default('DRAFT')
                                ->required(),
                        ])->columns(3),
                        
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

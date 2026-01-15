<?php

namespace App\Filament\Resources\PermintaanBarangs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PermintaanBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Permintaan')
                    ->schema([
                        Group::make([
                            TextInput::make('no_permintaan')
                                ->label('No Permintaan')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => 'PMT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)),
                            
                            Select::make('id_unit_kerja')
                                ->label('Unit Kerja')
                                ->relationship('unitKerja', 'nama_unit_kerja')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Reset pemohon saat unit kerja berubah
                                    $set('id_pemohon', null);
                                }),
                            
                            Select::make('id_pemohon')
                                ->label('Pemohon')
                                ->relationship('pemohon', 'nama_pegawai', fn ($query, $get) => 
                                    $query->where('id_unit_kerja', $get('id_unit_kerja'))
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->disabled(fn ($get) => !$get('id_unit_kerja'))
                                ->helperText('Pilih unit kerja terlebih dahulu'),
                        ])->columns(3),
                        
                        Group::make([
                            DatePicker::make('tanggal_permintaan')
                                ->label('Tanggal Permintaan')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                            
                            Select::make('jenis_permintaan')
                                ->label('Jenis Permintaan')
                                ->options([
                                    'RUTIN' => 'RUTIN',
                                    'DARURAT' => 'DARURAT',
                                    'KHUSUS' => 'KHUSUS',
                                ])
                                ->required()
                                ->default('RUTIN'),
                            
                            Select::make('status_permintaan')
                                ->label('Status Permintaan')
                                ->options([
                                    'DRAFT' => 'DRAFT',
                                    'PENDING' => 'PENDING',
                                    'DISETUJUI' => 'DISETUJUI',
                                    'DITOLAK' => 'DITOLAK',
                                    'DIPROSES' => 'DIPROSES',
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

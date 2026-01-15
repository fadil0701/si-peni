<?php

namespace App\Filament\Resources\PemeliharaanAsets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PemeliharaanAsetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pemeliharaan')
                    ->schema([
                        Group::make([
                            Select::make('id_item')
                                ->label('Inventory Item')
                                ->relationship('inventoryItem', 'kode_register')
                                ->searchable()
                                ->preload()
                                ->required(),
                            
                            Select::make('jenis_pemeliharaan')
                                ->label('Jenis Pemeliharaan')
                                ->options([
                                    'PREVENTIVE' => 'PREVENTIVE',
                                    'CORRECTIVE' => 'CORRECTIVE',
                                    'KALIBRASI' => 'KALIBRASI',
                                    'SERVICE' => 'SERVICE',
                                ])
                                ->required()
                                ->default('PREVENTIVE'),
                            
                            DatePicker::make('tanggal')
                                ->label('Tanggal Pemeliharaan')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                        ])->columns(3),
                        
                        Group::make([
                            TextInput::make('vendor')
                                ->label('Vendor/Service Provider')
                                ->maxLength(255),
                            
                            TextInput::make('biaya')
                                ->label('Biaya')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),
                        ])->columns(2),
                        
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        FileUpload::make('laporan_service')
                            ->label('Laporan Service')
                            ->directory('pemeliharaan/laporan')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        
                        // Hidden field untuk created_by
                        TextInput::make('created_by')
                            ->default(fn () => Auth::id())
                            ->hidden()
                            ->required(),
                    ]),
            ]);
    }
}

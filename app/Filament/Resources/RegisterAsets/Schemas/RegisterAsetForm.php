<?php

namespace App\Filament\Resources\RegisterAsets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RegisterAsetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Register Aset')
                    ->schema([
                        Group::make([
                            Select::make('id_inventory')
                                ->label('Data Inventory')
                                ->relationship('inventory', 'id_inventory', fn ($query) => 
                                    $query->where('jenis_inventory', 'ASET')
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Auto fill unit kerja dari inventory
                                    if ($state) {
                                        $inventory = \App\Models\DataInventory::find($state);
                                        if ($inventory && $inventory->gudang && $inventory->gudang->unitKerja) {
                                            $set('id_unit_kerja', $inventory->gudang->unitKerja->id_unit_kerja);
                                        }
                                    }
                                }),
                            
                            Select::make('id_unit_kerja')
                                ->label('Unit Kerja')
                                ->relationship('unitKerja', 'nama_unit_kerja')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])->columns(2),
                        
                        Group::make([
                            TextInput::make('nomor_register')
                                ->label('Nomor Register')
                                ->required()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true),
                            
                            Select::make('kondisi_aset')
                                ->label('Kondisi Aset')
                                ->options([
                                    'BAIK' => 'BAIK',
                                    'RUSAK_RINGAN' => 'RUSAK RINGAN',
                                    'RUSAK_BERAT' => 'RUSAK BERAT',
                                ])
                                ->default('BAIK')
                                ->required(),
                            
                            DatePicker::make('tanggal_perolehan')
                                ->label('Tanggal Perolehan')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                            
                            Select::make('status_aset')
                                ->label('Status Aset')
                                ->options([
                                    'AKTIF' => 'AKTIF',
                                    'NONAKTIF' => 'NONAKTIF',
                                    'HILANG' => 'HILANG',
                                    'RUSAK' => 'RUSAK',
                                    'DIHAPUS' => 'DIHAPUS',
                                ])
                                ->default('AKTIF')
                                ->required(),
                        ])->columns(4),
                    ]),
            ]);
    }
}

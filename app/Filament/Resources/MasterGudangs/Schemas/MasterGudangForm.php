<?php

namespace App\Filament\Resources\MasterGudangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class MasterGudangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('id_unit_kerja')
                        ->label('Unit Kerja')
                        ->relationship('unitKerja', 'nama_unit_kerja')
                        ->searchable()
                        ->preload()
                        ->required(),
                    
                    TextInput::make('nama_gudang')
                        ->label('Nama Gudang')
                        ->required()
                        ->maxLength(255),
                ])->columns(2),
                
                Group::make([
                    Select::make('jenis_gudang')
                        ->label('Jenis Gudang')
                        ->options([
                            'PUSAT' => 'PUSAT',
                            'UNIT' => 'UNIT',
                        ])
                        ->required()
                        ->default('PUSAT')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            // Reset kategori saat jenis berubah ke UNIT
                            if ($state === 'UNIT') {
                                $set('kategori_gudang', null);
                            } elseif ($state === 'PUSAT' && !$get('kategori_gudang')) {
                                $set('kategori_gudang', 'PERSEDIAAN');
                            }
                        })
                        ->helperText('PUSAT: dipisah per kategori. UNIT: menyimpan semua kategori'),
                    
                    Select::make('kategori_gudang')
                        ->label('Kategori Gudang')
                        ->options([
                            'ASET' => 'ASET',
                            'PERSEDIAAN' => 'PERSEDIAAN',
                            'FARMASI' => 'FARMASI',
                        ])
                        ->default('PERSEDIAAN')
                        ->required(fn ($get) => $get('jenis_gudang') === 'PUSAT')
                        ->hidden(fn ($get) => $get('jenis_gudang') === 'UNIT')
                        ->helperText('Hanya untuk Gudang Pusat. Gudang Unit menyimpan semua kategori secara logis.')
                        ->dehydrated(),
                ])->columns(2),
            ]);
    }
}

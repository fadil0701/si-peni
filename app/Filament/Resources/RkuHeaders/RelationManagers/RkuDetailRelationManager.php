<?php

namespace App\Filament\Resources\RkuHeaders\RelationManagers;

use App\Models\MasterDataBarang;
use App\Models\MasterSatuan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RkuDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'rkuDetail';

    protected static ?string $title = 'Detail RKU';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('id_data_barang')
                        ->label('Data Barang')
                        ->relationship('dataBarang', 'nama_barang')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($state) {
                                $barang = MasterDataBarang::find($state);
                                if ($barang && $barang->satuan) {
                                    $set('id_satuan', $barang->satuan->id_satuan);
                                }
                            }
                        }),
                    
                    Select::make('id_satuan')
                        ->label('Satuan')
                        ->relationship('satuan', 'nama_satuan')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled(fn ($get) => !$get('id_data_barang')),
                    
                    TextInput::make('qty_rencana')
                        ->label('Qty Rencana')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $qty = floatval($state ?? 0);
                            $harga = floatval($get('harga_satuan_rencana') ?? 0);
                            $set('subtotal_rencana', $qty * $harga);
                        }),
                ])->columns(3),
                
                Group::make([
                    TextInput::make('harga_satuan_rencana')
                        ->label('Harga Satuan Rencana')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $qty = floatval($get('qty_rencana') ?? 0);
                            $harga = floatval($state ?? 0);
                            $set('subtotal_rencana', $qty * $harga);
                        }),
                    
                    TextInput::make('subtotal_rencana')
                        ->label('Subtotal Rencana')
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(),
                    
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('dataBarang.nama_barang')
            ->columns([
                TextColumn::make('dataBarang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('qty_rencana')
                    ->label('Qty Rencana')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                
                TextColumn::make('satuan.nama_satuan')
                    ->label('Satuan')
                    ->sortable(),
                
                TextColumn::make('harga_satuan_rencana')
                    ->label('Harga Satuan')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('subtotal_rencana')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}


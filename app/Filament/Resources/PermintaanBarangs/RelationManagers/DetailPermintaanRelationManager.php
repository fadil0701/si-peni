<?php

namespace App\Filament\Resources\PermintaanBarangs\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DetailPermintaanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPermintaan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_data_barang')
                    ->label('Data Barang')
                    ->relationship('dataBarang', 'nama_barang')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                TextInput::make('qty_diminta')
                    ->label('Quantity Diminta')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),
                
                Select::make('id_satuan')
                    ->label('Satuan')
                    ->relationship('satuan', 'nama_satuan')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('dataBarang.nama_barang')
            ->columns([
                Tables\Columns\TextColumn::make('dataBarang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('qty_diminta')
                    ->label('Qty Diminta')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('satuan.nama_satuan')
                    ->label('Satuan')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id_detail_permintaan');
    }
}


<?php

namespace App\Filament\Resources\MasterDataBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterDataBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_data_barang')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('subjenisBarang.jenisBarang.kategoriBarang.kodeBarang.aset.nama_aset')
                    ->label('Aset')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('subjenisBarang.nama_subjenis_barang')
                    ->label('Sub Jenis')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('satuan.nama_satuan')
                    ->label('Satuan')
                    ->sortable(),
                
                TextColumn::make('merk')
                    ->label('Merk')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('dataInventory_count')
                    ->label('Jumlah Inventory')
                    ->counts('dataInventory')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_barang');
    }
}

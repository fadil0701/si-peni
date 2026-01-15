<?php

namespace App\Filament\Resources\MasterKodeBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterKodeBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('aset.nama_aset')
                    ->label('Aset')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kode_barang')
                    ->label('Kode Barang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_kode_barang')
                    ->label('Nama Kode Barang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kategoriBarang_count')
                    ->label('Jumlah Kategori')
                    ->counts('kategoriBarang')
                    ->sortable(),
                
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
            ->defaultSort('kode_barang');
    }
}

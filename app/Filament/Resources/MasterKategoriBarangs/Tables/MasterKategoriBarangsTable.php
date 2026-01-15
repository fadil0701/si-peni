<?php

namespace App\Filament\Resources\MasterKategoriBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterKategoriBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kodeBarang.aset.nama_aset')
                    ->label('Aset')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kodeBarang.nama_kode_barang')
                    ->label('Kode Barang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kode_kategori_barang')
                    ->label('Kode Kategori')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_kategori_barang')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jenisBarang_count')
                    ->label('Jumlah Jenis')
                    ->counts('jenisBarang')
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
            ->defaultSort('kode_kategori_barang');
    }
}

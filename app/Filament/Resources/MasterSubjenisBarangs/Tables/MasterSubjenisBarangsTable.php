<?php

namespace App\Filament\Resources\MasterSubjenisBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterSubjenisBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenisBarang.kategoriBarang.kodeBarang.aset.nama_aset')
                    ->label('Aset')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jenisBarang.nama_jenis_barang')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kode_subjenis_barang')
                    ->label('Kode Sub Jenis')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_subjenis_barang')
                    ->label('Nama Sub Jenis')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('dataBarang_count')
                    ->label('Jumlah Data Barang')
                    ->counts('dataBarang')
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
            ->defaultSort('kode_subjenis_barang');
    }
}

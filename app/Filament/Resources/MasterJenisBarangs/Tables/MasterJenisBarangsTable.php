<?php

namespace App\Filament\Resources\MasterJenisBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterJenisBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kategoriBarang.kodeBarang.aset.nama_aset')
                    ->label('Aset')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kategoriBarang.nama_kategori_barang')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kode_jenis_barang')
                    ->label('Kode Jenis')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_jenis_barang')
                    ->label('Nama Jenis')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('subjenisBarang_count')
                    ->label('Jumlah Sub Jenis')
                    ->counts('subjenisBarang')
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
            ->defaultSort('kode_jenis_barang');
    }
}

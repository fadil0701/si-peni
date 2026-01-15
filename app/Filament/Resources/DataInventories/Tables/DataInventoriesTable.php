<?php

namespace App\Filament\Resources\DataInventories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DataInventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dataBarang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('gudang.nama_gudang')
                    ->label('Gudang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jenis_inventory')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ASET' => 'info',
                        'PERSEDIAAN' => 'success',
                        'FARMASI' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('qty_input')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('satuan.nama_satuan')
                    ->label('Satuan')
                    ->sortable(),
                
                TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('tahun_anggaran')
                    ->label('Tahun')
                    ->sortable(),
                
                TextColumn::make('status_inventory')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'APPROVED' => 'success',
                        'DRAFT' => 'warning',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('inventoryItems_count')
                    ->label('Item Terdaftar')
                    ->counts('inventoryItems')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_inventory')
                    ->label('Jenis Inventory')
                    ->options([
                        'ASET' => 'ASET',
                        'PERSEDIAAN' => 'PERSEDIAAN',
                        'FARMASI' => 'FARMASI',
                    ]),
                
                SelectFilter::make('status_inventory')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'APPROVED' => 'APPROVED',
                        'REJECTED' => 'REJECTED',
                    ]),
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
            ->defaultSort('created_at', 'desc');
    }
}

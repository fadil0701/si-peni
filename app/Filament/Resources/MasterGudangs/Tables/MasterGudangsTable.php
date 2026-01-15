<?php

namespace App\Filament\Resources\MasterGudangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterGudangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unitKerja.nama_unit_kerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_gudang')
                    ->label('Nama Gudang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jenis_gudang')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PUSAT' => 'success',
                        'UNIT' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('kategori_gudang')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ASET' => 'warning',
                        'PERSEDIAAN' => 'success',
                        'FARMASI' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_gudang');
    }
}

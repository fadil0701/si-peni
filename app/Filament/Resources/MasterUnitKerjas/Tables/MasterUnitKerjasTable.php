<?php

namespace App\Filament\Resources\MasterUnitKerjas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterUnitKerjasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_unit_kerja')
                    ->label('Kode Unit Kerja')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_unit_kerja')
                    ->label('Nama Unit Kerja')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('ruangan_count')
                    ->label('Jumlah Ruangan')
                    ->counts('ruangan')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('gudang_count')
                    ->label('Jumlah Gudang')
                    ->counts('gudang')
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
            ->defaultSort('kode_unit_kerja');
    }
}

<?php

namespace App\Filament\Resources\RegisterAsets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegisterAsetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_register')
                    ->label('Nomor Register')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('inventory.dataBarang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('unitKerja.nama_unit_kerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                
                
                TextColumn::make('kondisi_aset')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BAIK' => 'success',
                        'RUSAK_RINGAN' => 'warning',
                        'RUSAK_BERAT' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('status_aset')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AKTIF' => 'success',
                        'NONAKTIF' => 'warning',
                        'HILANG' => 'danger',
                        'RUSAK' => 'danger',
                        'DIHAPUS' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('tanggal_perolehan')
                    ->label('Tanggal Perolehan')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kondisi_aset')
                    ->label('Kondisi')
                    ->options([
                        'BAIK' => 'BAIK',
                        'RUSAK_RINGAN' => 'RUSAK RINGAN',
                        'RUSAK_BERAT' => 'RUSAK BERAT',
                    ]),
                
                SelectFilter::make('status_aset')
                    ->label('Status')
                    ->options([
                        'AKTIF' => 'AKTIF',
                        'NONAKTIF' => 'NONAKTIF',
                        'HILANG' => 'HILANG',
                        'RUSAK' => 'RUSAK',
                        'DIHAPUS' => 'DIHAPUS',
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

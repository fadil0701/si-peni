<?php

namespace App\Filament\Resources\PemeliharaanAsets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PemeliharaanAsetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inventoryItem.kode_register')
                    ->label('Kode Register')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('inventoryItem.inventory.dataBarang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jenis_pemeliharaan')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PREVENTIVE' => 'success',
                        'CORRECTIVE' => 'warning',
                        'KALIBRASI' => 'info',
                        'SERVICE' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('biaya')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_pemeliharaan')
                    ->label('Jenis Pemeliharaan')
                    ->options([
                        'PREVENTIVE' => 'PREVENTIVE',
                        'CORRECTIVE' => 'CORRECTIVE',
                        'KALIBRASI' => 'KALIBRASI',
                        'SERVICE' => 'SERVICE',
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

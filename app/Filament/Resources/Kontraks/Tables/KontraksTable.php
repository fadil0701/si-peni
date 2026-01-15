<?php

namespace App\Filament\Resources\Kontraks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KontraksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_kontrak')
                    ->label('No. Kontrak')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('paket.nama_paket')
                    ->label('Paket Pengadaan')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('nama_vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('nilai_kontrak')
                    ->label('Nilai Kontrak')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('tanggal_kontrak')
                    ->label('Tanggal Kontrak')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('jenis_pembayaran')
                    ->label('Jenis Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'TUNAI' => 'success',
                        'UANG_MUKA' => 'info',
                        'TERMIN' => 'warning',
                        'PELUNASAN' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('status_kontrak')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DRAFT' => 'gray',
                        'AKTIF' => 'success',
                        'SELESAI' => 'info',
                        'DIBATALKAN' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('pembayaran_count')
                    ->label('Jumlah Pembayaran')
                    ->counts('pembayaran')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_kontrak')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'AKTIF' => 'AKTIF',
                        'SELESAI' => 'SELESAI',
                        'DIBATALKAN' => 'DIBATALKAN',
                    ]),
                
                SelectFilter::make('jenis_pembayaran')
                    ->label('Jenis Pembayaran')
                    ->options([
                        'TUNAI' => 'TUNAI',
                        'UANG_MUKA' => 'UANG MUKA',
                        'TERMIN' => 'TERMIN',
                        'PELUNASAN' => 'PELUNASAN',
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

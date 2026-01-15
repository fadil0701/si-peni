<?php

namespace App\Filament\Resources\Pembayarans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PembayaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_pembayaran')
                    ->label('No. Pembayaran')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kontrak.no_kontrak')
                    ->label('No. Kontrak')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kontrak.paket.nama_paket')
                    ->label('Paket')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                
                TextColumn::make('jenis_pembayaran')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'UANG_MUKA' => 'info',
                        'TERMIN' => 'warning',
                        'PELUNASAN' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('termin_ke')
                    ->label('Termin')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('total_pembayaran')
                    ->label('Total Pembayaran')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DRAFT' => 'gray',
                        'DIAJUKAN' => 'warning',
                        'DIVERIFIKASI' => 'info',
                        'DIBAYAR' => 'success',
                        'DITOLAK' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('verifikator.nama_pegawai')
                    ->label('Verifikator')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_pembayaran')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'DIAJUKAN' => 'DIAJUKAN',
                        'DIVERIFIKASI' => 'DIVERIFIKASI',
                        'DIBAYAR' => 'DIBAYAR',
                        'DITOLAK' => 'DITOLAK',
                    ]),
                
                SelectFilter::make('jenis_pembayaran')
                    ->label('Jenis')
                    ->options([
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

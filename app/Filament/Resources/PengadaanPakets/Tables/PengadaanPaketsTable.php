<?php

namespace App\Filament\Resources\PengadaanPakets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengadaanPaketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_paket')
                    ->label('No. Paket')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_paket')
                    ->label('Nama Paket')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                
                TextColumn::make('subKegiatan.nama_sub_kegiatan')
                    ->label('Sub Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(),
                
                TextColumn::make('rku.no_rku')
                    ->label('RKU')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('metode_pengadaan')
                    ->label('Metode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PEMILIHAN_LANGSUNG' => 'info',
                        'PENUNJUKAN_LANGSUNG' => 'warning',
                        'TENDER' => 'success',
                        'SWAKELOLA' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('nilai_paket')
                    ->label('Nilai Paket')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('status_paket')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DRAFT' => 'gray',
                        'DIAJUKAN' => 'warning',
                        'DIPROSES' => 'info',
                        'SELESAI' => 'success',
                        'DIBATALKAN' => 'danger',
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
                SelectFilter::make('status_paket')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'DIAJUKAN' => 'DIAJUKAN',
                        'DIPROSES' => 'DIPROSES',
                        'SELESAI' => 'SELESAI',
                        'DIBATALKAN' => 'DIBATALKAN',
                    ]),
                
                SelectFilter::make('metode_pengadaan')
                    ->label('Metode')
                    ->options([
                        'PEMILIHAN_LANGSUNG' => 'Pemilihan Langsung',
                        'PENUNJUKAN_LANGSUNG' => 'Penunjukan Langsung',
                        'TENDER' => 'Tender',
                        'SWAKELOLA' => 'Swakelola',
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

<?php

namespace App\Filament\Resources\PermintaanBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PermintaanBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_permintaan')
                    ->label('No Permintaan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('unitKerja.nama_unit_kerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('pemohon.nama_pegawai')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('tanggal_permintaan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('jenis_permintaan')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'RUTIN' => 'success',
                        'DARURAT' => 'danger',
                        'KHUSUS' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('status_permintaan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SELESAI' => 'success',
                        'DISETUJUI' => 'info',
                        'DIPROSES' => 'warning',
                        'PENDING' => 'warning',
                        'DRAFT' => 'gray',
                        'DITOLAK' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('detailPermintaan_count')
                    ->label('Jumlah Item')
                    ->counts('detailPermintaan')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_permintaan')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'PENDING' => 'PENDING',
                        'DISETUJUI' => 'DISETUJUI',
                        'DITOLAK' => 'DITOLAK',
                        'DIPROSES' => 'DIPROSES',
                        'SELESAI' => 'SELESAI',
                    ]),
                
                SelectFilter::make('jenis_permintaan')
                    ->label('Jenis Permintaan')
                    ->options([
                        'RUTIN' => 'RUTIN',
                        'DARURAT' => 'DARURAT',
                        'KHUSUS' => 'KHUSUS',
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

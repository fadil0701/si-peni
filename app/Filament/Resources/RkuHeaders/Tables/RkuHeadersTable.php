<?php

namespace App\Filament\Resources\RkuHeaders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RkuHeadersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_rku')
                    ->label('No. RKU')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('unitKerja.nama_unit_kerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('subKegiatan.nama_sub_kegiatan')
                    ->label('Sub Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('tahun_anggaran')
                    ->label('Tahun')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('jenis_rku')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BARANG' => 'info',
                        'ASET' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('status_rku')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DRAFT' => 'gray',
                        'DIAJUKAN' => 'warning',
                        'DISETUJUI' => 'success',
                        'DITOLAK' => 'danger',
                        'DIPROSES' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('total_anggaran')
                    ->label('Total Anggaran')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('pengaju.nama_pegawai')
                    ->label('Pengaju')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_rku')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'DIAJUKAN' => 'DIAJUKAN',
                        'DISETUJUI' => 'DISETUJUI',
                        'DITOLAK' => 'DITOLAK',
                        'DIPROSES' => 'DIPROSES',
                    ]),
                
                SelectFilter::make('jenis_rku')
                    ->label('Jenis')
                    ->options([
                        'BARANG' => 'BARANG',
                        'ASET' => 'ASET',
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

<?php

namespace App\Filament\Resources\PenerimaanBarangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PenerimaanBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_penerimaan')
                    ->label('No Penerimaan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('distribusi.no_sbbk')
                    ->label('No SBBK')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('unitKerja.nama_unit_kerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('pegawaiPenerima.nama_pegawai')
                    ->label('Penerima')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('tanggal_penerimaan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('status_penerimaan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SELESAI' => 'success',
                        'DITERIMA' => 'info',
                        'DRAFT' => 'gray',
                        'DITOLAK' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('detailPenerimaan_count')
                    ->label('Jumlah Item')
                    ->counts('detailPenerimaan')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_penerimaan')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'DITERIMA' => 'DITERIMA',
                        'DITOLAK' => 'DITOLAK',
                        'SELESAI' => 'SELESAI',
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

<?php

namespace App\Filament\Resources\TransaksiDistribusis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransaksiDistribusisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_sbbk')
                    ->label('No SBBK')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('permintaan.no_permintaan')
                    ->label('No Permintaan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('tanggal_distribusi')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('gudangAsal.nama_gudang')
                    ->label('Gudang Asal')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('gudangTujuan.nama_gudang')
                    ->label('Gudang Tujuan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('pegawaiPengirim.nama_pegawai')
                    ->label('Pengirim')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('status_distribusi')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SELESAI' => 'success',
                        'TERKIRIM' => 'info',
                        'DIPROSES' => 'warning',
                        'DRAFT' => 'gray',
                        'BATAL' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('detailDistribusi_count')
                    ->label('Jumlah Item')
                    ->counts('detailDistribusi')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_distribusi')
                    ->label('Status')
                    ->options([
                        'DRAFT' => 'DRAFT',
                        'DIPROSES' => 'DIPROSES',
                        'TERKIRIM' => 'TERKIRIM',
                        'SELESAI' => 'SELESAI',
                        'BATAL' => 'BATAL',
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

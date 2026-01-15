<?php

namespace App\Filament\Resources\MasterPegawais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterPegawaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip_pegawai')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_pegawai')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('unitKerja.nama_unit_kerja')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email_pegawai')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('no_telp')
                    ->label('No Telepon')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('nama_pegawai');
    }
}

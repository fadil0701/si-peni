<?php

namespace App\Filament\Resources\DataInventories\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'inventoryItems';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_register')
                    ->label('Kode Register')
                    ->required()
                    ->maxLength(255),
                TextInput::make('no_seri')
                    ->label('No Seri')
                    ->maxLength(255),
                Select::make('kondisi_item')
                    ->label('Kondisi Item')
                    ->options([
                        'BAIK' => 'BAIK',
                        'RUSAK_RINGAN' => 'RUSAK RINGAN',
                        'RUSAK_BERAT' => 'RUSAK BERAT',
                    ])
                    ->default('BAIK')
                    ->required(),
                Select::make('status_item')
                    ->label('Status Item')
                    ->options([
                        'AKTIF' => 'AKTIF',
                        'NONAKTIF' => 'NONAKTIF',
                        'HILANG' => 'HILANG',
                        'RUSAK' => 'RUSAK',
                    ])
                    ->default('AKTIF')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('kode_register')
            ->columns([
                Tables\Columns\TextColumn::make('kode_register')
                    ->label('Kode Register')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_seri')
                    ->label('No Seri')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kondisi_item')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BAIK' => 'success',
                        'RUSAK_RINGAN' => 'warning',
                        'RUSAK_BERAT' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_item')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AKTIF' => 'success',
                        'NONAKTIF' => 'warning',
                        'HILANG' => 'danger',
                        'RUSAK' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('gudang.nama_gudang')
                    ->label('Gudang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangan.nama_ruangan')
                    ->label('Ruangan')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kondisi_item')
                    ->label('Kondisi')
                    ->options([
                        'BAIK' => 'BAIK',
                        'RUSAK_RINGAN' => 'RUSAK RINGAN',
                        'RUSAK_BERAT' => 'RUSAK BERAT',
                    ]),
                Tables\Filters\SelectFilter::make('status_item')
                    ->label('Status')
                    ->options([
                        'AKTIF' => 'AKTIF',
                        'NONAKTIF' => 'NONAKTIF',
                        'HILANG' => 'HILANG',
                        'RUSAK' => 'RUSAK',
                    ]),
            ])
            ->headerActions([
                // Tidak perlu create manual karena auto register
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('kode_register');
    }
}


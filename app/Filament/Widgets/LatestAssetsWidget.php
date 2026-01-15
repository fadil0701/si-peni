<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAssetsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InventoryItem::query()
                    ->whereHas('inventory', function ($query) {
                        $query->where('jenis_inventory', 'ASET');
                    })
                    ->with(['inventory.dataBarang', 'ruangan'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('inventory.dataBarang.nama_barang')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kode_register')
                    ->label('Kode Aset')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('ruangan.nama_ruangan')
                    ->label('Lokasi')
                    ->default('-')
                    ->sortable(),
                
                TextColumn::make('kondisi_item')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BAIK' => 'success',
                        'RUSAK_RINGAN' => 'warning',
                        'RUSAK_BERAT' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state === 'BAIK' ? 'Baik' : $state),
            ])
            ->heading('Inventaris Aset Terbaru')
            ->description('Daftar aset yang baru ditambahkan ke sistem')
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}


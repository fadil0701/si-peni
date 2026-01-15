<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiDistribusi;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TransactionHistoryWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransaksiDistribusi::query()
                    ->latest('tanggal_distribusi')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('no_sbbk')
                    ->label('ID Transaksi')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jenis_transaksi')
                    ->label('Jenis Transaksi')
                    ->default('Distribusi (SBBK)')
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('tanggal_distribusi')
                    ->label('Tanggal')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->heading('Riwayat Transaksi Terakhir')
            ->description('Daftar transaksi terbaru di sistem')
            ->defaultSort('tanggal_distribusi', 'desc')
            ->paginated(false);
    }
}

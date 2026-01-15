<?php

namespace App\Filament\Resources\DataInventories\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DataInventoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->schema([
                        TextEntry::make('dataBarang.nama_barang')
                            ->label('Nama Barang'),
                        TextEntry::make('gudang.nama_gudang')
                            ->label('Gudang'),
                        TextEntry::make('jenis_inventory')
                            ->label('Jenis Inventory')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'ASET' => 'info',
                                'PERSEDIAAN' => 'success',
                                'FARMASI' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('tahun_anggaran')
                            ->label('Tahun Anggaran'),
                    ])
                    ->columns(2),
                
                Section::make('Kuantitas & Harga')
                    ->schema([
                        TextEntry::make('qty_input')
                            ->label('Quantity Input')
                            ->numeric(),
                        TextEntry::make('satuan.nama_satuan')
                            ->label('Satuan'),
                        TextEntry::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->money('IDR'),
                        TextEntry::make('total_harga')
                            ->label('Total Harga')
                            ->money('IDR'),
                    ])
                    ->columns(4),
                
                Section::make('Informasi Teknis')
                    ->schema([
                        TextEntry::make('merk')
                            ->label('Merk'),
                        TextEntry::make('tipe')
                            ->label('Tipe'),
                        TextEntry::make('tahun_produksi')
                            ->label('Tahun Produksi'),
                        TextEntry::make('spesifikasi')
                            ->label('Spesifikasi')
                            ->columnSpanFull(),
                        TextEntry::make('no_seri')
                            ->label('No Seri'),
                        TextEntry::make('no_batch')
                            ->label('No Batch'),
                        TextEntry::make('tanggal_kedaluwarsa')
                            ->label('Tanggal Kedaluwarsa')
                            ->date('d/m/Y'),
                    ])
                    ->columns(3)
                    ->collapsible(),
                
                Section::make('Status & Informasi Lainnya')
                    ->schema([
                        TextEntry::make('status_inventory')
                            ->label('Status Inventory')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'APPROVED' => 'success',
                                'DRAFT' => 'warning',
                                'REJECTED' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('sumberAnggaran.nama_anggaran')
                            ->label('Sumber Anggaran'),
                        TextEntry::make('subKegiatan.nama_sub_kegiatan')
                            ->label('Sub Kegiatan'),
                        TextEntry::make('inventoryItems_count')
                            ->label('Item Terdaftar')
                            ->counts('inventoryItems'),
                        TextEntry::make('creator.name')
                            ->label('Dibuat Oleh'),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}

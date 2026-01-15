<?php

namespace App\Filament\Resources\PermintaanBarangs\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PermintaanBarangInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Permintaan')
                    ->schema([
                        TextEntry::make('no_permintaan')
                            ->label('No Permintaan'),
                        TextEntry::make('unitKerja.nama_unit_kerja')
                            ->label('Unit Kerja'),
                        TextEntry::make('pemohon.nama_pegawai')
                            ->label('Pemohon'),
                        TextEntry::make('tanggal_permintaan')
                            ->label('Tanggal Permintaan')
                            ->date('d/m/Y'),
                        TextEntry::make('jenis_permintaan')
                            ->label('Jenis Permintaan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'RUTIN' => 'success',
                                'DARURAT' => 'danger',
                                'KHUSUS' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('status_permintaan')
                            ->label('Status Permintaan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'SELESAI' => 'success',
                                'DISETUJUI' => 'info',
                                'DIPROSES' => 'warning',
                                'PENDING' => 'warning',
                                'DRAFT' => 'gray',
                                'DITOLAK' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                        TextEntry::make('detailPermintaan_count')
                            ->label('Jumlah Item')
                            ->counts('detailPermintaan'),
                    ])
                    ->columns(3),
            ]);
    }
}

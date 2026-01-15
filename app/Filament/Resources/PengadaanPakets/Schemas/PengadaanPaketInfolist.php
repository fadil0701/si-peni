<?php

namespace App\Filament\Resources\PengadaanPakets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PengadaanPaketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_sub_kegiatan')
                    ->numeric(),
                TextEntry::make('id_rku')
                    ->numeric(),
                TextEntry::make('no_paket'),
                TextEntry::make('nama_paket'),
                TextEntry::make('metode_pengadaan'),
                TextEntry::make('nilai_paket')
                    ->numeric(),
                TextEntry::make('tanggal_mulai')
                    ->date(),
                TextEntry::make('tanggal_selesai')
                    ->date(),
                TextEntry::make('status_paket'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}

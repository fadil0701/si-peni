<?php

namespace App\Filament\Resources\Kontraks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KontrakInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_paket')
                    ->numeric(),
                TextEntry::make('no_kontrak'),
                TextEntry::make('no_sp'),
                TextEntry::make('no_po'),
                TextEntry::make('nama_vendor'),
                TextEntry::make('npwp_vendor'),
                TextEntry::make('nilai_kontrak')
                    ->numeric(),
                TextEntry::make('tanggal_kontrak')
                    ->date(),
                TextEntry::make('tanggal_mulai')
                    ->date(),
                TextEntry::make('tanggal_selesai')
                    ->date(),
                TextEntry::make('jenis_pembayaran'),
                TextEntry::make('jumlah_termin')
                    ->numeric(),
                TextEntry::make('status_kontrak'),
                TextEntry::make('upload_dokumen'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}

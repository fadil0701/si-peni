<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PembayaranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_kontrak')
                    ->numeric(),
                TextEntry::make('no_pembayaran'),
                TextEntry::make('jenis_pembayaran'),
                TextEntry::make('termin_ke')
                    ->numeric(),
                TextEntry::make('nilai_pembayaran')
                    ->numeric(),
                TextEntry::make('ppn')
                    ->numeric(),
                TextEntry::make('pph')
                    ->numeric(),
                TextEntry::make('total_pembayaran')
                    ->numeric(),
                TextEntry::make('tanggal_pembayaran')
                    ->date(),
                TextEntry::make('status_pembayaran'),
                TextEntry::make('id_verifikator')
                    ->numeric(),
                TextEntry::make('tanggal_verifikasi')
                    ->date(),
                TextEntry::make('no_bukti_bayar'),
                TextEntry::make('upload_bukti_bayar'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}

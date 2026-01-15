<?php

namespace App\Filament\Resources\RkuHeaders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RkuHeaderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_unit_kerja')
                    ->numeric(),
                TextEntry::make('id_sub_kegiatan')
                    ->numeric(),
                TextEntry::make('no_rku'),
                TextEntry::make('tahun_anggaran'),
                TextEntry::make('tanggal_pengajuan')
                    ->date(),
                TextEntry::make('jenis_rku'),
                TextEntry::make('status_rku'),
                TextEntry::make('id_pengaju')
                    ->numeric(),
                TextEntry::make('id_approver')
                    ->numeric(),
                TextEntry::make('tanggal_approval')
                    ->date(),
                TextEntry::make('total_anggaran')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}

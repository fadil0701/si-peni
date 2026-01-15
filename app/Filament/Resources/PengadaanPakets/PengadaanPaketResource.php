<?php

namespace App\Filament\Resources\PengadaanPakets;

use App\Filament\Resources\PengadaanPakets\Pages\CreatePengadaanPaket;
use App\Filament\Resources\PengadaanPakets\Pages\EditPengadaanPaket;
use App\Filament\Resources\PengadaanPakets\Pages\ListPengadaanPakets;
use App\Filament\Resources\PengadaanPakets\Pages\ViewPengadaanPaket;
use App\Filament\Resources\PengadaanPakets\Schemas\PengadaanPaketForm;
use App\Filament\Resources\PengadaanPakets\Schemas\PengadaanPaketInfolist;
use App\Filament\Resources\PengadaanPakets\Tables\PengadaanPaketsTable;
use App\Models\PengadaanPaket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengadaanPaketResource extends Resource
{
    protected static ?string $model = PengadaanPaket::class;

    protected static ?string $navigationLabel = 'Paket Pengadaan';
    protected static ?string $modelLabel = 'Paket Pengadaan';
    protected static ?string $pluralModelLabel = 'Paket Pengadaan';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Pengadaan';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    public static function form(Schema $schema): Schema
    {
        return PengadaanPaketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PengadaanPaketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengadaanPaketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengadaanPakets::route('/'),
            'create' => CreatePengadaanPaket::route('/create'),
            'view' => ViewPengadaanPaket::route('/{record}'),
            'edit' => EditPengadaanPaket::route('/{record}/edit'),
        ];
    }
}

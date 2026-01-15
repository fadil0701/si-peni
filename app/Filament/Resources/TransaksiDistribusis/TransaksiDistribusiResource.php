<?php

namespace App\Filament\Resources\TransaksiDistribusis;

use App\Filament\Resources\TransaksiDistribusis\Pages\CreateTransaksiDistribusi;
use App\Filament\Resources\TransaksiDistribusis\Pages\EditTransaksiDistribusi;
use App\Filament\Resources\TransaksiDistribusis\Pages\ListTransaksiDistribusis;
use App\Filament\Resources\TransaksiDistribusis\Schemas\TransaksiDistribusiForm;
use App\Filament\Resources\TransaksiDistribusis\Tables\TransaksiDistribusisTable;
use App\Models\TransaksiDistribusi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransaksiDistribusiResource extends Resource
{
    protected static ?string $model = TransaksiDistribusi::class;

    protected static ?string $navigationLabel = 'Distribusi Barang';
    
    protected static ?string $modelLabel = 'Distribusi Barang';
    
    protected static ?string $pluralModelLabel = 'Distribusi Barang';
    
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Permintaan & Transaksi';
    }

    public static function form(Schema $schema): Schema
    {
        return TransaksiDistribusiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksiDistribusisTable::configure($table);
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
            'index' => ListTransaksiDistribusis::route('/'),
            'create' => CreateTransaksiDistribusi::route('/create'),
            'edit' => EditTransaksiDistribusi::route('/{record}/edit'),
        ];
    }
}

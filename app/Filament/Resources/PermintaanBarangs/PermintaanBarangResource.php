<?php

namespace App\Filament\Resources\PermintaanBarangs;

use App\Filament\Resources\PermintaanBarangs\Pages\CreatePermintaanBarang;
use App\Filament\Resources\PermintaanBarangs\Pages\EditPermintaanBarang;
use App\Filament\Resources\PermintaanBarangs\Pages\ListPermintaanBarangs;
use App\Filament\Resources\PermintaanBarangs\Pages\ViewPermintaanBarang;
use App\Filament\Resources\PermintaanBarangs\RelationManagers\DetailPermintaanRelationManager;
use App\Filament\Resources\PermintaanBarangs\Schemas\PermintaanBarangForm;
use App\Filament\Resources\PermintaanBarangs\Schemas\PermintaanBarangInfolist;
use App\Filament\Resources\PermintaanBarangs\Tables\PermintaanBarangsTable;
use App\Models\PermintaanBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PermintaanBarangResource extends Resource
{
    protected static ?string $model = PermintaanBarang::class;

    protected static ?string $navigationLabel = 'Permintaan Barang';
    
    protected static ?string $modelLabel = 'Permintaan Barang';
    
    protected static ?string $pluralModelLabel = 'Permintaan Barang';
    
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Permintaan & Transaksi';
    }

    public static function form(Schema $schema): Schema
    {
        return PermintaanBarangForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermintaanBarangInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermintaanBarangsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DetailPermintaanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermintaanBarangs::route('/'),
            'create' => CreatePermintaanBarang::route('/create'),
            'view' => ViewPermintaanBarang::route('/{record}'),
            'edit' => EditPermintaanBarang::route('/{record}/edit'),
        ];
    }
}

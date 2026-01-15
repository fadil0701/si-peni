<?php

namespace App\Filament\Resources\MasterKategoriBarangs;

use App\Filament\Resources\MasterKategoriBarangs\Pages\CreateMasterKategoriBarang;
use App\Filament\Resources\MasterKategoriBarangs\Pages\EditMasterKategoriBarang;
use App\Filament\Resources\MasterKategoriBarangs\Pages\ListMasterKategoriBarangs;
use App\Filament\Resources\MasterKategoriBarangs\Schemas\MasterKategoriBarangForm;
use App\Filament\Resources\MasterKategoriBarangs\Tables\MasterKategoriBarangsTable;
use App\Models\MasterKategoriBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterKategoriBarangResource extends Resource
{
    protected static ?string $model = MasterKategoriBarang::class;

    protected static ?string $navigationLabel = 'Kategori Barang';
    protected static ?string $modelLabel = 'Kategori Barang';
    protected static ?string $pluralModelLabel = 'Kategori Barang';
    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterKategoriBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterKategoriBarangsTable::configure($table);
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
            'index' => ListMasterKategoriBarangs::route('/'),
            'create' => CreateMasterKategoriBarang::route('/create'),
            'edit' => EditMasterKategoriBarang::route('/{record}/edit'),
        ];
    }
}

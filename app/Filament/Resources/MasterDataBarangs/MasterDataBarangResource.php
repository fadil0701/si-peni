<?php

namespace App\Filament\Resources\MasterDataBarangs;

use App\Filament\Resources\MasterDataBarangs\Pages\CreateMasterDataBarang;
use App\Filament\Resources\MasterDataBarangs\Pages\EditMasterDataBarang;
use App\Filament\Resources\MasterDataBarangs\Pages\ListMasterDataBarangs;
use App\Filament\Resources\MasterDataBarangs\Schemas\MasterDataBarangForm;
use App\Filament\Resources\MasterDataBarangs\Tables\MasterDataBarangsTable;
use App\Models\MasterDataBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterDataBarangResource extends Resource
{
    protected static ?string $model = MasterDataBarang::class;

    protected static ?string $navigationLabel = 'Data Barang';
    protected static ?string $modelLabel = 'Data Barang';
    protected static ?string $pluralModelLabel = 'Data Barang';
    protected static ?int $navigationSort = 9;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterDataBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterDataBarangsTable::configure($table);
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
            'index' => ListMasterDataBarangs::route('/'),
            'create' => CreateMasterDataBarang::route('/create'),
            'edit' => EditMasterDataBarang::route('/{record}/edit'),
        ];
    }
}

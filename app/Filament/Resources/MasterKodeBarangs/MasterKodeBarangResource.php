<?php

namespace App\Filament\Resources\MasterKodeBarangs;

use App\Filament\Resources\MasterKodeBarangs\Pages\CreateMasterKodeBarang;
use App\Filament\Resources\MasterKodeBarangs\Pages\EditMasterKodeBarang;
use App\Filament\Resources\MasterKodeBarangs\Pages\ListMasterKodeBarangs;
use App\Filament\Resources\MasterKodeBarangs\Schemas\MasterKodeBarangForm;
use App\Filament\Resources\MasterKodeBarangs\Tables\MasterKodeBarangsTable;
use App\Models\MasterKodeBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterKodeBarangResource extends Resource
{
    protected static ?string $model = MasterKodeBarang::class;

    protected static ?string $navigationLabel = 'Kode Barang';
    protected static ?string $modelLabel = 'Kode Barang';
    protected static ?string $pluralModelLabel = 'Kode Barang';
    protected static ?int $navigationSort = 3;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHashtag;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterKodeBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterKodeBarangsTable::configure($table);
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
            'index' => ListMasterKodeBarangs::route('/'),
            'create' => CreateMasterKodeBarang::route('/create'),
            'edit' => EditMasterKodeBarang::route('/{record}/edit'),
        ];
    }
}

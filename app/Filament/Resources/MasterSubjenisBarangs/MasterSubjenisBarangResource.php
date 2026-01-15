<?php

namespace App\Filament\Resources\MasterSubjenisBarangs;

use App\Filament\Resources\MasterSubjenisBarangs\Pages\CreateMasterSubjenisBarang;
use App\Filament\Resources\MasterSubjenisBarangs\Pages\EditMasterSubjenisBarang;
use App\Filament\Resources\MasterSubjenisBarangs\Pages\ListMasterSubjenisBarangs;
use App\Filament\Resources\MasterSubjenisBarangs\Schemas\MasterSubjenisBarangForm;
use App\Filament\Resources\MasterSubjenisBarangs\Tables\MasterSubjenisBarangsTable;
use App\Models\MasterSubjenisBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterSubjenisBarangResource extends Resource
{
    protected static ?string $model = MasterSubjenisBarang::class;

    protected static ?string $navigationLabel = 'Sub Jenis Barang';
    protected static ?string $modelLabel = 'Sub Jenis Barang';
    protected static ?string $pluralModelLabel = 'Sub Jenis Barang';
    protected static ?int $navigationSort = 6;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterSubjenisBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterSubjenisBarangsTable::configure($table);
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
            'index' => ListMasterSubjenisBarangs::route('/'),
            'create' => CreateMasterSubjenisBarang::route('/create'),
            'edit' => EditMasterSubjenisBarang::route('/{record}/edit'),
        ];
    }
}

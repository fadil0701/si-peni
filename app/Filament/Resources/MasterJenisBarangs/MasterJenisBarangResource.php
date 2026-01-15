<?php

namespace App\Filament\Resources\MasterJenisBarangs;

use App\Filament\Resources\MasterJenisBarangs\Pages\CreateMasterJenisBarang;
use App\Filament\Resources\MasterJenisBarangs\Pages\EditMasterJenisBarang;
use App\Filament\Resources\MasterJenisBarangs\Pages\ListMasterJenisBarangs;
use App\Filament\Resources\MasterJenisBarangs\Schemas\MasterJenisBarangForm;
use App\Filament\Resources\MasterJenisBarangs\Tables\MasterJenisBarangsTable;
use App\Models\MasterJenisBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterJenisBarangResource extends Resource
{
    protected static ?string $model = MasterJenisBarang::class;

    protected static ?string $navigationLabel = 'Jenis Barang';
    protected static ?string $modelLabel = 'Jenis Barang';
    protected static ?string $pluralModelLabel = 'Jenis Barang';
    protected static ?int $navigationSort = 5;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterJenisBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterJenisBarangsTable::configure($table);
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
            'index' => ListMasterJenisBarangs::route('/'),
            'create' => CreateMasterJenisBarang::route('/create'),
            'edit' => EditMasterJenisBarang::route('/{record}/edit'),
        ];
    }
}

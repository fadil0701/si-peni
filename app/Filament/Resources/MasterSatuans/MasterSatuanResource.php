<?php

namespace App\Filament\Resources\MasterSatuans;

use App\Filament\Resources\MasterSatuans\Pages\CreateMasterSatuan;
use App\Filament\Resources\MasterSatuans\Pages\EditMasterSatuan;
use App\Filament\Resources\MasterSatuans\Pages\ListMasterSatuans;
use App\Filament\Resources\MasterSatuans\Schemas\MasterSatuanForm;
use App\Filament\Resources\MasterSatuans\Tables\MasterSatuansTable;
use App\Models\MasterSatuan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterSatuanResource extends Resource
{
    protected static ?string $model = MasterSatuan::class;

    protected static ?string $navigationLabel = 'Satuan';
    protected static ?string $modelLabel = 'Satuan';
    protected static ?string $pluralModelLabel = 'Satuan';
    protected static ?int $navigationSort = 7;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterSatuanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterSatuansTable::configure($table);
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
            'index' => ListMasterSatuans::route('/'),
            'create' => CreateMasterSatuan::route('/create'),
            'edit' => EditMasterSatuan::route('/{record}/edit'),
        ];
    }
}

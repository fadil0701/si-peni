<?php

namespace App\Filament\Resources\MasterRuangans;

use App\Filament\Resources\MasterRuangans\Pages\CreateMasterRuangan;
use App\Filament\Resources\MasterRuangans\Pages\EditMasterRuangan;
use App\Filament\Resources\MasterRuangans\Pages\ListMasterRuangans;
use App\Filament\Resources\MasterRuangans\Schemas\MasterRuanganForm;
use App\Filament\Resources\MasterRuangans\Tables\MasterRuangansTable;
use App\Models\MasterRuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterRuanganResource extends Resource
{
    protected static ?string $model = MasterRuangan::class;

    protected static ?string $navigationLabel = 'Ruangan';
    protected static ?string $modelLabel = 'Ruangan';
    protected static ?string $pluralModelLabel = 'Ruangan';
    protected static ?int $navigationSort = 3;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Manajemen';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterRuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterRuangansTable::configure($table);
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
            'index' => ListMasterRuangans::route('/'),
            'create' => CreateMasterRuangan::route('/create'),
            'edit' => EditMasterRuangan::route('/{record}/edit'),
        ];
    }
}

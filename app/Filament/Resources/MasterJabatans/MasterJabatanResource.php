<?php

namespace App\Filament\Resources\MasterJabatans;

use App\Filament\Resources\MasterJabatans\Pages\CreateMasterJabatan;
use App\Filament\Resources\MasterJabatans\Pages\EditMasterJabatan;
use App\Filament\Resources\MasterJabatans\Pages\ListMasterJabatans;
use App\Filament\Resources\MasterJabatans\Schemas\MasterJabatanForm;
use App\Filament\Resources\MasterJabatans\Tables\MasterJabatansTable;
use App\Models\MasterJabatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterJabatanResource extends Resource
{
    protected static ?string $model = MasterJabatan::class;

    protected static ?string $navigationLabel = 'Jabatan';
    protected static ?string $modelLabel = 'Jabatan';
    protected static ?string $pluralModelLabel = 'Jabatan';
    protected static ?int $navigationSort = 5;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Manajemen';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterJabatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterJabatansTable::configure($table);
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
            'index' => ListMasterJabatans::route('/'),
            'create' => CreateMasterJabatan::route('/create'),
            'edit' => EditMasterJabatan::route('/{record}/edit'),
        ];
    }
}

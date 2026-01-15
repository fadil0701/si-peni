<?php

namespace App\Filament\Resources\MasterUnitKerjas;

use App\Filament\Resources\MasterUnitKerjas\Pages\CreateMasterUnitKerja;
use App\Filament\Resources\MasterUnitKerjas\Pages\EditMasterUnitKerja;
use App\Filament\Resources\MasterUnitKerjas\Pages\ListMasterUnitKerjas;
use App\Filament\Resources\MasterUnitKerjas\Schemas\MasterUnitKerjaForm;
use App\Filament\Resources\MasterUnitKerjas\Tables\MasterUnitKerjasTable;
use App\Models\MasterUnitKerja;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterUnitKerjaResource extends Resource
{
    protected static ?string $model = MasterUnitKerja::class;

    protected static ?string $navigationLabel = 'Unit Kerja';
    
    protected static ?string $modelLabel = 'Unit Kerja';
    
    protected static ?string $pluralModelLabel = 'Unit Kerja';
    
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Manajemen';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MasterUnitKerjaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterUnitKerjasTable::configure($table);
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
            'index' => ListMasterUnitKerjas::route('/'),
            'create' => CreateMasterUnitKerja::route('/create'),
            'edit' => EditMasterUnitKerja::route('/{record}/edit'),
        ];
    }
}

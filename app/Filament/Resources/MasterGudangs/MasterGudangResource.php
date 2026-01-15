<?php

namespace App\Filament\Resources\MasterGudangs;

use App\Filament\Resources\MasterGudangs\Pages\CreateMasterGudang;
use App\Filament\Resources\MasterGudangs\Pages\EditMasterGudang;
use App\Filament\Resources\MasterGudangs\Pages\ListMasterGudangs;
use App\Filament\Resources\MasterGudangs\Schemas\MasterGudangForm;
use App\Filament\Resources\MasterGudangs\Tables\MasterGudangsTable;
use App\Models\MasterGudang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterGudangResource extends Resource
{
    protected static ?string $model = MasterGudang::class;

    protected static ?string $navigationLabel = 'Gudang';
    
    protected static ?string $modelLabel = 'Gudang';
    
    protected static ?string $pluralModelLabel = 'Gudang';
    
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Manajemen';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MasterGudangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterGudangsTable::configure($table);
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
            'index' => ListMasterGudangs::route('/'),
            'create' => CreateMasterGudang::route('/create'),
            'edit' => EditMasterGudang::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\DataInventories;

use App\Filament\Resources\DataInventories\Pages\CreateDataInventory;
use App\Filament\Resources\DataInventories\Pages\EditDataInventory;
use App\Filament\Resources\DataInventories\Pages\ListDataInventories;
use App\Filament\Resources\DataInventories\Pages\ViewDataInventory;
use App\Filament\Resources\DataInventories\RelationManagers\InventoryItemsRelationManager;
use App\Filament\Resources\DataInventories\Schemas\DataInventoryForm;
use App\Filament\Resources\DataInventories\Schemas\DataInventoryInfolist;
use App\Filament\Resources\DataInventories\Tables\DataInventoriesTable;
use App\Models\DataInventory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DataInventoryResource extends Resource
{
    protected static ?string $model = DataInventory::class;

    protected static ?string $navigationLabel = 'Data Inventory';
    
    protected static ?string $modelLabel = 'Data Inventory';
    
    protected static ?string $pluralModelLabel = 'Data Inventory';
    
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return DataInventoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DataInventoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DataInventoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            InventoryItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDataInventories::route('/'),
            'create' => CreateDataInventory::route('/create'),
            'view' => ViewDataInventory::route('/{record}'),
            'edit' => EditDataInventory::route('/{record}/edit'),
        ];
    }
}

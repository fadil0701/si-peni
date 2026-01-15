<?php

namespace App\Filament\Resources\RkuHeaders;

use App\Filament\Resources\RkuHeaders\Pages\CreateRkuHeader;
use App\Filament\Resources\RkuHeaders\Pages\EditRkuHeader;
use App\Filament\Resources\RkuHeaders\Pages\ListRkuHeaders;
use App\Filament\Resources\RkuHeaders\Pages\ViewRkuHeader;
use App\Filament\Resources\RkuHeaders\RelationManagers;
use App\Filament\Resources\RkuHeaders\Schemas\RkuHeaderForm;
use App\Filament\Resources\RkuHeaders\Schemas\RkuHeaderInfolist;
use App\Filament\Resources\RkuHeaders\Tables\RkuHeadersTable;
use App\Models\RkuHeader;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RkuHeaderResource extends Resource
{
    protected static ?string $model = RkuHeader::class;

    protected static ?string $navigationLabel = 'RKU';
    protected static ?string $modelLabel = 'Rencana Kebutuhan Unit';
    protected static ?string $pluralModelLabel = 'Rencana Kebutuhan Unit';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Perencanaan';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function form(Schema $schema): Schema
    {
        return RkuHeaderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RkuHeaderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RkuHeadersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RkuDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRkuHeaders::route('/'),
            'create' => CreateRkuHeader::route('/create'),
            'view' => ViewRkuHeader::route('/{record}'),
            'edit' => EditRkuHeader::route('/{record}/edit'),
        ];
    }
}

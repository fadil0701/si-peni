<?php

namespace App\Filament\Resources\MasterAsets;

use App\Filament\Resources\MasterAsets\Pages\CreateMasterAset;
use App\Filament\Resources\MasterAsets\Pages\EditMasterAset;
use App\Filament\Resources\MasterAsets\Pages\ListMasterAsets;
use App\Filament\Resources\MasterAsets\Schemas\MasterAsetForm;
use App\Filament\Resources\MasterAsets\Tables\MasterAsetsTable;
use App\Models\MasterAset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterAsetResource extends Resource
{
    protected static ?string $model = MasterAset::class;

    protected static ?string $navigationLabel = 'Aset';
    protected static ?string $modelLabel = 'Aset';
    protected static ?string $pluralModelLabel = 'Aset';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterAsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterAsetsTable::configure($table);
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
            'index' => ListMasterAsets::route('/'),
            'create' => CreateMasterAset::route('/create'),
            'edit' => EditMasterAset::route('/{record}/edit'),
        ];
    }
}

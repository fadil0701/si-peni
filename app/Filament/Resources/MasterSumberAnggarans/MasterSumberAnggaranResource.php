<?php

namespace App\Filament\Resources\MasterSumberAnggarans;

use App\Filament\Resources\MasterSumberAnggarans\Pages\CreateMasterSumberAnggaran;
use App\Filament\Resources\MasterSumberAnggarans\Pages\EditMasterSumberAnggaran;
use App\Filament\Resources\MasterSumberAnggarans\Pages\ListMasterSumberAnggarans;
use App\Filament\Resources\MasterSumberAnggarans\Schemas\MasterSumberAnggaranForm;
use App\Filament\Resources\MasterSumberAnggarans\Tables\MasterSumberAnggaransTable;
use App\Models\MasterSumberAnggaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterSumberAnggaranResource extends Resource
{
    protected static ?string $model = MasterSumberAnggaran::class;

    protected static ?string $navigationLabel = 'Sumber Anggaran';
    protected static ?string $modelLabel = 'Sumber Anggaran';
    protected static ?string $pluralModelLabel = 'Sumber Anggaran';
    protected static ?int $navigationSort = 8;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterSumberAnggaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterSumberAnggaransTable::configure($table);
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
            'index' => ListMasterSumberAnggarans::route('/'),
            'create' => CreateMasterSumberAnggaran::route('/create'),
            'edit' => EditMasterSumberAnggaran::route('/{record}/edit'),
        ];
    }
}

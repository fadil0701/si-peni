<?php

namespace App\Filament\Resources\MasterKegiatans;

use App\Filament\Resources\MasterKegiatans\Pages\CreateMasterKegiatan;
use App\Filament\Resources\MasterKegiatans\Pages\EditMasterKegiatan;
use App\Filament\Resources\MasterKegiatans\Pages\ListMasterKegiatans;
use App\Filament\Resources\MasterKegiatans\Schemas\MasterKegiatanForm;
use App\Filament\Resources\MasterKegiatans\Tables\MasterKegiatansTable;
use App\Models\MasterKegiatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterKegiatanResource extends Resource
{
    protected static ?string $model = MasterKegiatan::class;

    protected static ?string $navigationLabel = 'Kegiatan';
    protected static ?string $modelLabel = 'Kegiatan';
    protected static ?string $pluralModelLabel = 'Kegiatan';
    protected static ?int $navigationSort = 7;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Manajemen';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterKegiatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterKegiatansTable::configure($table);
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
            'index' => ListMasterKegiatans::route('/'),
            'create' => CreateMasterKegiatan::route('/create'),
            'edit' => EditMasterKegiatan::route('/{record}/edit'),
        ];
    }
}

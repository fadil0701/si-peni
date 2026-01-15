<?php

namespace App\Filament\Resources\MasterSubKegiatans;

use App\Filament\Resources\MasterSubKegiatans\Pages\CreateMasterSubKegiatan;
use App\Filament\Resources\MasterSubKegiatans\Pages\EditMasterSubKegiatan;
use App\Filament\Resources\MasterSubKegiatans\Pages\ListMasterSubKegiatans;
use App\Filament\Resources\MasterSubKegiatans\Schemas\MasterSubKegiatanForm;
use App\Filament\Resources\MasterSubKegiatans\Tables\MasterSubKegiatansTable;
use App\Models\MasterSubKegiatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterSubKegiatanResource extends Resource
{
    protected static ?string $model = MasterSubKegiatan::class;

    protected static ?string $navigationLabel = 'Sub Kegiatan';
    protected static ?string $modelLabel = 'Sub Kegiatan';
    protected static ?string $pluralModelLabel = 'Sub Kegiatan';
    protected static ?int $navigationSort = 8;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Manajemen';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterSubKegiatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterSubKegiatansTable::configure($table);
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
            'index' => ListMasterSubKegiatans::route('/'),
            'create' => CreateMasterSubKegiatan::route('/create'),
            'edit' => EditMasterSubKegiatan::route('/{record}/edit'),
        ];
    }
}

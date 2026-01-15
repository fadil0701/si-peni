<?php

namespace App\Filament\Resources\MasterPegawais;

use App\Filament\Resources\MasterPegawais\Pages\CreateMasterPegawai;
use App\Filament\Resources\MasterPegawais\Pages\EditMasterPegawai;
use App\Filament\Resources\MasterPegawais\Pages\ListMasterPegawais;
use App\Filament\Resources\MasterPegawais\Schemas\MasterPegawaiForm;
use App\Filament\Resources\MasterPegawais\Tables\MasterPegawaisTable;
use App\Models\MasterPegawai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterPegawaiResource extends Resource
{
    protected static ?string $model = MasterPegawai::class;

    protected static ?string $navigationLabel = 'Pegawai';
    protected static ?string $modelLabel = 'Pegawai';
    protected static ?string $pluralModelLabel = 'Pegawai';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterPegawaiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterPegawaisTable::configure($table);
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
            'index' => ListMasterPegawais::route('/'),
            'create' => CreateMasterPegawai::route('/create'),
            'edit' => EditMasterPegawai::route('/{record}/edit'),
        ];
    }
}

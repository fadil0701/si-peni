<?php

namespace App\Filament\Resources\MasterPrograms;

use App\Filament\Resources\MasterPrograms\Pages\CreateMasterProgram;
use App\Filament\Resources\MasterPrograms\Pages\EditMasterProgram;
use App\Filament\Resources\MasterPrograms\Pages\ListMasterPrograms;
use App\Filament\Resources\MasterPrograms\Schemas\MasterProgramForm;
use App\Filament\Resources\MasterPrograms\Tables\MasterProgramsTable;
use App\Models\MasterProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MasterProgramResource extends Resource
{
    protected static ?string $model = MasterProgram::class;

    protected static ?string $navigationLabel = 'Program';
    protected static ?string $modelLabel = 'Program';
    protected static ?string $pluralModelLabel = 'Program';
    protected static ?int $navigationSort = 6;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Manajemen';
    }

    public static function form(Schema $schema): Schema
    {
        return MasterProgramForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterProgramsTable::configure($table);
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
            'index' => ListMasterPrograms::route('/'),
            'create' => CreateMasterProgram::route('/create'),
            'edit' => EditMasterProgram::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\RegisterAsets;

use App\Filament\Resources\RegisterAsets\Pages\CreateRegisterAset;
use App\Filament\Resources\RegisterAsets\Pages\EditRegisterAset;
use App\Filament\Resources\RegisterAsets\Pages\ListRegisterAsets;
use App\Filament\Resources\RegisterAsets\Schemas\RegisterAsetForm;
use App\Filament\Resources\RegisterAsets\Tables\RegisterAsetsTable;
use App\Models\RegisterAset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RegisterAsetResource extends Resource
{
    protected static ?string $model = RegisterAset::class;

    protected static ?string $navigationLabel = 'Register Aset';
    
    protected static ?string $modelLabel = 'Register Aset';
    
    protected static ?string $pluralModelLabel = 'Register Aset';
    
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Aset & KIR';
    }

    public static function form(Schema $schema): Schema
    {
        return RegisterAsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegisterAsetsTable::configure($table);
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
            'index' => ListRegisterAsets::route('/'),
            'create' => CreateRegisterAset::route('/create'),
            'edit' => EditRegisterAset::route('/{record}/edit'),
        ];
    }
}

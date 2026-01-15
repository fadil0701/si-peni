<?php

namespace App\Filament\Resources\PemeliharaanAsets;

use App\Filament\Resources\PemeliharaanAsets\Pages\CreatePemeliharaanAset;
use App\Filament\Resources\PemeliharaanAsets\Pages\EditPemeliharaanAset;
use App\Filament\Resources\PemeliharaanAsets\Pages\ListPemeliharaanAsets;
use App\Filament\Resources\PemeliharaanAsets\Schemas\PemeliharaanAsetForm;
use App\Filament\Resources\PemeliharaanAsets\Tables\PemeliharaanAsetsTable;
use App\Models\PemeliharaanAset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PemeliharaanAsetResource extends Resource
{
    protected static ?string $model = PemeliharaanAset::class;

    protected static ?string $navigationLabel = 'Pemeliharaan Aset';
    
    protected static ?string $modelLabel = 'Pemeliharaan Aset';
    
    protected static ?string $pluralModelLabel = 'Pemeliharaan Aset';
    
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Pemeliharaan & Kalibrasi';
    }

    public static function form(Schema $schema): Schema
    {
        return PemeliharaanAsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemeliharaanAsetsTable::configure($table);
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
            'index' => ListPemeliharaanAsets::route('/'),
            'create' => CreatePemeliharaanAset::route('/create'),
            'edit' => EditPemeliharaanAset::route('/{record}/edit'),
        ];
    }
}

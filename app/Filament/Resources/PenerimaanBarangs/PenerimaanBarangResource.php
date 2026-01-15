<?php

namespace App\Filament\Resources\PenerimaanBarangs;

use App\Filament\Resources\PenerimaanBarangs\Pages\CreatePenerimaanBarang;
use App\Filament\Resources\PenerimaanBarangs\Pages\EditPenerimaanBarang;
use App\Filament\Resources\PenerimaanBarangs\Pages\ListPenerimaanBarangs;
use App\Filament\Resources\PenerimaanBarangs\Schemas\PenerimaanBarangForm;
use App\Filament\Resources\PenerimaanBarangs\Tables\PenerimaanBarangsTable;
use App\Models\PenerimaanBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PenerimaanBarangResource extends Resource
{
    protected static ?string $model = PenerimaanBarang::class;

    protected static ?string $navigationLabel = 'Penerimaan Barang';
    
    protected static ?string $modelLabel = 'Penerimaan Barang';
    
    protected static ?string $pluralModelLabel = 'Penerimaan Barang';
    
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationGroup(): ?string
    {
        return 'Permintaan & Transaksi';
    }

    public static function form(Schema $schema): Schema
    {
        return PenerimaanBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenerimaanBarangsTable::configure($table);
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
            'index' => ListPenerimaanBarangs::route('/'),
            'create' => CreatePenerimaanBarang::route('/create'),
            'edit' => EditPenerimaanBarang::route('/{record}/edit'),
        ];
    }
}

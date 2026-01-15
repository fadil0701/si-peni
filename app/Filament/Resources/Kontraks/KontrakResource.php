<?php

namespace App\Filament\Resources\Kontraks;

use App\Filament\Resources\Kontraks\Pages\CreateKontrak;
use App\Filament\Resources\Kontraks\Pages\EditKontrak;
use App\Filament\Resources\Kontraks\Pages\ListKontraks;
use App\Filament\Resources\Kontraks\Pages\ViewKontrak;
use App\Filament\Resources\Kontraks\Schemas\KontrakForm;
use App\Filament\Resources\Kontraks\Schemas\KontrakInfolist;
use App\Filament\Resources\Kontraks\Tables\KontraksTable;
use App\Models\Kontrak;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KontrakResource extends Resource
{
    protected static ?string $model = Kontrak::class;

    protected static ?string $navigationLabel = 'Kontrak / SP / PO';
    protected static ?string $modelLabel = 'Kontrak';
    protected static ?string $pluralModelLabel = 'Kontrak';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Pengadaan';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    public static function form(Schema $schema): Schema
    {
        return KontrakForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KontrakInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KontraksTable::configure($table);
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
            'index' => ListKontraks::route('/'),
            'create' => CreateKontrak::route('/create'),
            'view' => ViewKontrak::route('/{record}'),
            'edit' => EditKontrak::route('/{record}/edit'),
        ];
    }
}

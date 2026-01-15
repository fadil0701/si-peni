<?php

namespace App\Filament\Resources\MasterUnitKerjas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class MasterUnitKerjaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    TextInput::make('kode_unit_kerja')
                        ->label('Kode Unit Kerja')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),
                    
                    TextInput::make('nama_unit_kerja')
                        ->label('Nama Unit Kerja')
                        ->required()
                        ->maxLength(255),
                ])->columns(2),
            ]);
    }
}

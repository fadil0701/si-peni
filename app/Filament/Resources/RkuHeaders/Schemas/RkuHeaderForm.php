<?php

namespace App\Filament\Resources\RkuHeaders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RkuHeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi RKU')
                    ->schema([
                        Group::make([
                            Select::make('id_unit_kerja')
                                ->label('Unit Kerja')
                                ->relationship('unitKerja', 'nama_unit_kerja')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive(),
                            
                            Select::make('id_sub_kegiatan')
                                ->label('Sub Kegiatan')
                                ->relationship('subKegiatan', 'nama_sub_kegiatan', fn ($query, $get) => 
                                    $query->when($get('id_unit_kerja'), function ($q) use ($get) {
                                        // Filter sub kegiatan berdasarkan unit kerja jika diperlukan
                                    })
                                )
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])->columns(2),
                        
                        Group::make([
                            TextInput::make('no_rku')
                                ->label('No. RKU')
                                ->required()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true)
                                ->default(fn () => 'RKU-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))
                                ->helperText('Nomor RKU akan otomatis terisi'),
                            
                            TextInput::make('tahun_anggaran')
                                ->label('Tahun Anggaran')
                                ->required()
                                ->maxLength(4)
                                ->default(date('Y'))
                                ->numeric(),
                            
                            DatePicker::make('tanggal_pengajuan')
                                ->label('Tanggal Pengajuan')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y'),
                            
                            Select::make('jenis_rku')
                                ->label('Jenis RKU')
                                ->options([
                                    'BARANG' => 'BARANG',
                                    'ASET' => 'ASET',
                                ])
                                ->required()
                                ->default('BARANG'),
                        ])->columns(4),
                    ]),
                
                Section::make('Status & Approval')
                    ->schema([
                        Group::make([
                            Select::make('status_rku')
                                ->label('Status RKU')
                                ->options([
                                    'DRAFT' => 'DRAFT',
                                    'DIAJUKAN' => 'DIAJUKAN',
                                    'DISETUJUI' => 'DISETUJUI',
                                    'DITOLAK' => 'DITOLAK',
                                    'DIPROSES' => 'DIPROSES',
                                ])
                                ->required()
                                ->default('DRAFT')
                                ->disabled(fn ($record) => $record && in_array($record->status_rku, ['DISETUJUI', 'DITOLAK'])),
                            
                            Select::make('id_pengaju')
                                ->label('Pengaju')
                                ->relationship('pengaju', 'nama_pegawai', fn ($query, $get) => 
                                    $query->when($get('id_unit_kerja'), function ($q) use ($get) {
                                        $q->where('id_unit_kerja', $get('id_unit_kerja'));
                                    })
                                )
                                ->searchable()
                                ->preload(),
                            
                            Select::make('id_approver')
                                ->label('Approver')
                                ->relationship('approver', 'nama_pegawai')
                                ->searchable()
                                ->preload()
                                ->disabled(fn ($get) => $get('status_rku') !== 'DIAJUKAN'),
                            
                            DatePicker::make('tanggal_approval')
                                ->label('Tanggal Approval')
                                ->displayFormat('d/m/Y')
                                ->disabled(fn ($get) => !$get('id_approver')),
                        ])->columns(4),
                        
                        Textarea::make('catatan_approval')
                            ->label('Catatan Approval')
                            ->rows(2)
                            ->columnSpanFull()
                            ->disabled(fn ($get) => !$get('id_approver')),
                    ]),
                
                Section::make('Informasi Tambahan')
                    ->schema([
                        Group::make([
                            TextInput::make('total_anggaran')
                                ->label('Total Anggaran')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->disabled()
                                ->dehydrated()
                                ->helperText('Total akan dihitung otomatis dari detail RKU'),
                            
                            Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}

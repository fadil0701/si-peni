<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegisterAset extends Model
{
    protected $table = 'register_aset';
    protected $primaryKey = 'id_register_aset';
    public $timestamps = true;

    protected $fillable = [
        'id_inventory',
        'id_unit_kerja',
        'nomor_register',
        'kondisi_aset',
        'tanggal_perolehan',
        'status_aset',
    ];

    protected $casts = [
        'kondisi_aset' => 'string',
        'status_aset' => 'string',
        'tanggal_perolehan' => 'date',
    ];

    // Relationships
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(DataInventory::class, 'id_inventory', 'id_inventory');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(MasterUnitKerja::class, 'id_unit_kerja', 'id_unit_kerja');
    }

    public function kartuInventarisRuangan(): HasMany
    {
        return $this->hasMany(KartuInventarisRuangan::class, 'id_register_aset', 'id_register_aset');
    }

    public function mutasiAset(): HasMany
    {
        return $this->hasMany(MutasiAset::class, 'id_register_aset', 'id_register_aset');
    }
}

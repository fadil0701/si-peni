<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class InventoryItem extends Model
{
    protected $table = 'inventory_item';
    protected $primaryKey = 'id_item';
    public $timestamps = true;

    protected $fillable = [
        'id_inventory',
        'kode_register',
        'no_seri',
        'kondisi_item',
        'status_item',
        'id_gudang',
        'id_ruangan',
        'qr_code',
    ];

    protected $casts = [
        'kondisi_item' => 'string',
        'status_item' => 'string',
    ];

    // Relationships
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(DataInventory::class, 'id_inventory', 'id_inventory');
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(MasterGudang::class, 'id_gudang', 'id_gudang');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(MasterRuangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function registerAset(): HasMany
    {
        // RegisterAset menggunakan id_inventory, jadi kita perlu relasi melalui inventory
        return $this->hasMany(RegisterAset::class, 'id_inventory', 'id_inventory');
    }

    public function kartuInventarisRuangan()
    {
        // Relasi melalui RegisterAset jika ada, atau langsung jika ada id_ruangan
        return $this->hasOneThrough(
            KartuInventarisRuangan::class,
            RegisterAset::class,
            'id_item', // Foreign key on RegisterAset table
            'id_register_aset', // Foreign key on KartuInventarisRuangan table
            'id_item', // Local key on InventoryItem table
            'id_register_aset' // Local key on RegisterAset table
        );
    }

    public function pemeliharaanAset(): HasMany
    {
        return $this->hasMany(PemeliharaanAset::class, 'id_item', 'id_item');
    }
}

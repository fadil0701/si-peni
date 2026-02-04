<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Schema;

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
        // RegisterAset menggunakan id_inventory untuk backward compatibility
        // Controller akan handle filtering berdasarkan id_item jika kolom sudah ada
        return $this->hasMany(RegisterAset::class, 'id_inventory', 'id_inventory');
    }
    
    /**
     * Relasi registerAset berdasarkan id_item (jika kolom sudah ada)
     * Gunakan ini untuk query yang lebih tepat setelah migration
     */
    public function registerAsetByItem(): HasMany
    {
        return $this->hasMany(RegisterAset::class, 'id_item', 'id_item');
    }
    
    /**
     * Get single RegisterAset untuk InventoryItem ini (jika ada)
     * Untuk backward compatibility dengan data lama yang mungkin belum punya id_item
     */
    public function singleRegisterAset()
    {
        $hasIdItemColumn = Schema::hasColumn('register_aset', 'id_item');
        
        if ($hasIdItemColumn) {
            // Cek berdasarkan id_item dulu (lebih tepat)
            $registerAset = RegisterAset::where('id_item', $this->id_item)->first();
            
            // Jika tidak ada dan id_item null di RegisterAset, cek berdasarkan id_inventory
            // (untuk backward compatibility dengan data lama)
            if (!$registerAset) {
                $registerAset = RegisterAset::where('id_inventory', $this->id_inventory)
                    ->whereNull('id_item')
                    ->first();
            }
        } else {
            // Fallback untuk data lama: cek berdasarkan id_inventory saja
            $registerAset = RegisterAset::where('id_inventory', $this->id_inventory)->first();
        }
        
        return $registerAset;
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

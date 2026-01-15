<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataStock extends Model
{
    protected $table = 'data_stock';
    protected $primaryKey = 'id_stock';
    public $timestamps = true;

    protected $fillable = [
        'id_data_barang',
        'id_gudang',
        'qty_awal',
        'qty_masuk',
        'qty_keluar',
        'qty_akhir',
        'id_satuan',
        'last_updated',
    ];

    protected $casts = [
        'qty_awal' => 'decimal:2',
        'qty_masuk' => 'decimal:2',
        'qty_keluar' => 'decimal:2',
        'qty_akhir' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    // Relationships
    public function dataBarang(): BelongsTo
    {
        return $this->belongsTo(MasterDataBarang::class, 'id_data_barang', 'id_data_barang');
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(MasterGudang::class, 'id_gudang', 'id_gudang');
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(MasterSatuan::class, 'id_satuan', 'id_satuan');
    }
}

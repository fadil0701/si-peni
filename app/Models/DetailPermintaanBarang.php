<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPermintaanBarang extends Model
{
    protected $table = 'detail_permintaan_barang';
    protected $primaryKey = 'id_detail_permintaan';
    public $timestamps = true;

    protected $fillable = [
        'id_permintaan',
        'id_data_barang',
        'qty_diminta',
        'id_satuan',
        'keterangan',
    ];

    protected $casts = [
        'qty_diminta' => 'decimal:2',
    ];

    // Relationships
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanBarang::class, 'id_permintaan', 'id_permintaan');
    }

    public function dataBarang(): BelongsTo
    {
        return $this->belongsTo(MasterDataBarang::class, 'id_data_barang', 'id_data_barang');
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(MasterSatuan::class, 'id_satuan', 'id_satuan');
    }
}

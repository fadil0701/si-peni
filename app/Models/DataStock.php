<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class, 'id_stock', 'id_stock');
    }

    /**
     * Get total stock available for a barang across all gudang
     */
    public static function getTotalStock($idDataBarang): float
    {
        return self::where('id_data_barang', $idDataBarang)
            ->sum('qty_akhir') ?? 0;
    }

    /**
     * Get stock per gudang for a barang
     */
    public static function getStockPerGudang($idDataBarang): \Illuminate\Support\Collection
    {
        return self::where('id_data_barang', $idDataBarang)
            ->with('gudang', 'satuan')
            ->get()
            ->map(function($stock) {
                return [
                    'id_gudang' => $stock->id_gudang,
                    'nama_gudang' => $stock->gudang->nama_gudang ?? '-',
                    'qty_akhir' => $stock->qty_akhir,
                    'satuan' => $stock->satuan->nama_satuan ?? '-',
                ];
            });
    }
}

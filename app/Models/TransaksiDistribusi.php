<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiDistribusi extends Model
{
    protected $table = 'transaksi_distribusi';
    protected $primaryKey = 'id_distribusi';
    public $timestamps = true;

    protected $fillable = [
        'no_sbbk',
        'id_permintaan',
        'tanggal_distribusi',
        'id_gudang_asal',
        'id_gudang_tujuan',
        'id_pegawai_pengirim',
        'status_distribusi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_distribusi' => 'datetime',
        'status_distribusi' => 'string',
    ];

    // Relationships
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanBarang::class, 'id_permintaan', 'id_permintaan');
    }

    public function gudangAsal(): BelongsTo
    {
        return $this->belongsTo(MasterGudang::class, 'id_gudang_asal', 'id_gudang');
    }

    public function gudangTujuan(): BelongsTo
    {
        return $this->belongsTo(MasterGudang::class, 'id_gudang_tujuan', 'id_gudang');
    }

    public function pegawaiPengirim(): BelongsTo
    {
        return $this->belongsTo(MasterPegawai::class, 'id_pegawai_pengirim', 'id');
    }

    public function detailDistribusi(): HasMany
    {
        return $this->hasMany(DetailDistribusi::class, 'id_distribusi', 'id_distribusi');
    }

    public function penerimaanBarang(): HasMany
    {
        return $this->hasMany(PenerimaanBarang::class, 'id_distribusi', 'id_distribusi');
    }
}

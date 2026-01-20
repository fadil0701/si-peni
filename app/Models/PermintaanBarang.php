<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';
    protected $primaryKey = 'id_permintaan';
    public $timestamps = true;

    protected $fillable = [
        'no_permintaan',
        'id_unit_kerja',
        'id_pemohon',
        'tanggal_permintaan',
        'jenis_permintaan',
        'status_permintaan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'jenis_permintaan' => 'array', // Cast sebagai array untuk JSON
        'status_permintaan' => 'string',
    ];

    // Relationships
    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(MasterUnitKerja::class, 'id_unit_kerja', 'id_unit_kerja');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(MasterPegawai::class, 'id_pemohon', 'id');
    }

    public function detailPermintaan(): HasMany
    {
        return $this->hasMany(DetailPermintaanBarang::class, 'id_permintaan', 'id_permintaan');
    }

    public function approval(): HasMany
    {
        return $this->hasMany(ApprovalPermintaan::class, 'id_referensi', 'id_permintaan')
            ->where('modul_approval', 'PERMINTAAN_BARANG');
    }

    public function transaksiDistribusi(): HasMany
    {
        return $this->hasMany(TransaksiDistribusi::class, 'id_permintaan', 'id_permintaan');
    }

    public function draftDetailDistribusi(): HasMany
    {
        return $this->hasMany(DraftDetailDistribusi::class, 'id_permintaan', 'id_permintaan');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RekamMedis extends Model
{
    protected $table = 'rekam_medis';

    protected $fillable = [
        'pasien_id', 'dokter_id', 'tanggal', 'keluhan', 'diagnosa', 'tindakan', 'biaya_periksa',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    public function resep(): HasMany
    {
        return $this->hasMany(Resep::class, 'rekam_medis_id');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'rekam_medis_id');
    }

    /**
     * Hitung ulang total pembayaran rekam medis ini:
     * total = biaya_periksa + SUM(resep.jumlah * obat.harga).
     */
    public function recomputePembayaran(): void
    {
        $totalObat = (int) $this->resep()
            ->join('obat', 'obat.id', '=', 'resep.obat_id')
            ->selectRaw('COALESCE(SUM(resep.jumlah * obat.harga), 0) AS total_obat')
            ->value('total_obat');

        $this->pembayaran()->update([
            'total' => (int) $this->biaya_periksa + $totalObat,
        ]);
    }
}

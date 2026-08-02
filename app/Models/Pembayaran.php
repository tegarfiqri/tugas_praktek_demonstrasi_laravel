<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = ['rekam_medis_id', 'tanggal', 'total', 'status'];

    public function rekamMedis(): BelongsTo
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }
}

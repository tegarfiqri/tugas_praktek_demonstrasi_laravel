<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dokter extends Model
{
    protected $table = 'dokter';

    protected $fillable = ['nama', 'spesialis', 'telepon'];

    public function rekamMedis(): HasMany
    {
        return $this->hasMany(RekamMedis::class, 'dokter_id');
    }
}

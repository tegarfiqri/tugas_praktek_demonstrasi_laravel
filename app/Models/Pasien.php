<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    protected $table = 'pasien';

    protected $fillable = ['nama', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'telepon'];

    public function rekamMedis(): HasMany
    {
        return $this->hasMany(RekamMedis::class, 'pasien_id');
    }
}

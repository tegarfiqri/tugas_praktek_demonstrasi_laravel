<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = ['nama_obat', 'harga', 'stok', 'satuan'];

    public function resep(): HasMany
    {
        return $this->hasMany(Resep::class, 'obat_id');
    }
}

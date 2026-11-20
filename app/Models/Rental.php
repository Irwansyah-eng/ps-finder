<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    protected $fillable = ['nama', 'kecamatan', 'alamat', 'lat', 'lng', 'jam', 'harga', 'wa'];

    protected $casts = ['lat' => 'float', 'lng' => 'float', 'harga' => 'integer'];

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}

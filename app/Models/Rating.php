<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'rental_id', 'kecamatan', 'harga', 'nyaman', 'konsol', 'game',
        'fasilitas', 'bersih', 'layanan', 'lokasi', 'puas',
    ];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }
}

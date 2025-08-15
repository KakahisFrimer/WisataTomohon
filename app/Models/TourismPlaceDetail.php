<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourismPlaceDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl__wisata_details';

    protected $fillable = [
        'wisata_id',
        'hari__buka',
        'jam__buka',
        'jam__tutup',
        'harga__tiket',
    ];

    public function wisata()
    {
        return $this->belongsTo(TourismPlace::class, 'wisata_id', 'id__tmpt');
    }
}

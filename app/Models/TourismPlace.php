<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourismPlace extends Model
{
    use HasFactory;

    protected $table = 'tbl__wisata';
    protected $primaryKey = 'id__tmpt';

    protected $fillable = [
        'name',
        'address',
        'category',
        'contact',
        'description',
        'foto__tmpt',
        'longitude',
        'latitude'
    ];

    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(TourismPlaceDetail::class, 'wisata_id', 'id__tmpt');
    }
}

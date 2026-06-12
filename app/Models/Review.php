<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'wisata_id',
        'user_id',
        'rating',
        'feedback',
        'image'
    ];

    public function tourismPlace()
    {
        return $this->belongsTo(TourismPlace::class, 'wisata_id', 'id__tmpt');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}

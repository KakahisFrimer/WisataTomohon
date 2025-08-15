<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'tbl__events'; // ini penting biar cocok sama tabel aslinya
    protected $primaryKey = 'id__event'; // primary key tabelnya

    protected $fillable = [
        'wisata_id',
        'nama_event',
        'tanggal_event',
    ];

    public function wisata()
    {
        return $this->belongsTo(\App\Models\TourismPlace::class, 'wisata_id', 'id__tmpt');
    }
}



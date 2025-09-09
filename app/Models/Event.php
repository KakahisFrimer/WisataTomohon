<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'tbl__events';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'description',
        'category',
        'location',
        'start_date',
        'end_date',
        'status',
        'foto__event'
    ];
    // timestamps default true
}

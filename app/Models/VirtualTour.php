<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualTour extends Model
{
    protected $fillable = [
        'judul', 
        'keterangan',
        'foto',
        'status',
    ];
}

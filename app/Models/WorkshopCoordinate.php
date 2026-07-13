<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkshopCoordinate extends Model
{
    use HasFactory;

    protected $table = 'workshop_coordinates';

    protected $fillable = [
        'latitude',
        'longitude',
    ];
}

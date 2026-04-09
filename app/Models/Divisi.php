<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    //
    protected $table = 'divisi';
    protected $primaryKey = 'id_divisi';
    public $timestamps = true;

    // Fillable attributes
    protected $fillable = [
        'nama_divisi',
    ];
}


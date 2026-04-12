<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatuanHarga extends Model
{
    protected $table = 'satuan_harga';
    protected $primaryKey = 'id_satuan';
    public $timestamps = true;

    // Fillable attributes
    protected $fillable = [
        'nama_satuan',
    ];
}

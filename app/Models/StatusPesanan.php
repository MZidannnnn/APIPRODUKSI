<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPesanan extends Model
{
    protected $table = 'status_pesanan';
    protected $primaryKey = 'id_status_pesanan';
    public $timestamps = true;

    // Fillable attributes
    protected $fillable = [
        'nama_status_pesanan',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    //
    protected $table = 'jenis_pembayaran';
    protected $primaryKey = 'id_jenis_pembayaran';
    public $timestamps = true;

    // Fillable attributes
    protected $fillable = [
        'nama_jenis_pembayaran',
    ];
}

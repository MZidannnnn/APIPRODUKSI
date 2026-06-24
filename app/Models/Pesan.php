<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $table = 'pesan';
    protected $primaryKey = 'id_pesan';

    protected $fillable = [
        'id_percakapan',
        'id_pengirim',
        'isi_pesan',
        'dibaca_pada',
    ];

    protected $casts = [
        'dibaca_pada' => 'datetime',
    ];

    public function percakapan()
    {
        return $this->belongsTo(Percakapan::class, 'id_percakapan', 'id_percakapan');
    }

    public function pengirim()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengirim', 'id_pengguna');
    }

    public function lampiran()
    {
        return $this->hasMany(PesanLampiran::class, 'id_pesan', 'id_pesan');
    }
}

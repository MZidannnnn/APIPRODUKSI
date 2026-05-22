<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesanLampiran extends Model
{
    protected $table = 'pesan_lampiran';
    protected $primaryKey = 'id_lampiran';

    protected $fillable = [
        'id_pesan',
        'jenis',
        'disk',
        'path',
        'original_name',
        'stored_name',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'checksum',
    ];

    protected $casts = [
        'size_bytes' => 'int',
        'width' => 'int',
        'height' => 'int',
    ];

    public function pesan()
    {
        return $this->belongsTo(Pesan::class, 'id_pesan', 'id_pesan');
    }
}

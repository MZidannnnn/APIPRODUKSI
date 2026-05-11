<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersetujuanHarga extends Model
{
    protected $table = 'persetujuan_harga';
    protected $primaryKey = 'id_persetujuan';
    public $timestamps = true;

    protected $fillable = [
        'id_pesanan',
        'harga_awal',
        'harga_tawaran',
        'harga_disetujui',
        'status_persetujuan',
        'catatan',
        'tanggal_persetujuan',
    ];

    protected $casts = [
        'harga_awal' => 'decimal:2',
        'harga_tawaran' => 'decimal:2',
        'harga_disetujui' => 'decimal:2',
        'tanggal_persetujuan' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
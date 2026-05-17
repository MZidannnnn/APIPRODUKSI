<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Percakapan extends Model
{
    protected $table = 'percakapan';
    protected $primaryKey = 'id_percakapan';

    protected $fillable = [
        'id_pengguna',
        'id_pesanan',
        'id_item_produksi',
        'id_kategori',
        'terakhir_aktif',
    ];

    protected $casts = [
        'terakhir_aktif' => 'datetime',
    ];

    public function itemProduksi()
    {
        return $this->belongsTo(ItemProduksi::class, 'id_item_produksi', 'id_item_produksi');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriUsaha::class, 'id_kategori', 'id_kategori');
    }

    public function pengguna()
    {
        return $this->belongsTo(pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function pesan()
    {
        return $this->hasMany(Pesan::class, 'id_percakapan', 'id_percakapan');
    }
}

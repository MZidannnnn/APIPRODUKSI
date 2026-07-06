<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Percakapan extends Model
{
    protected $table = 'percakapan';
    protected $primaryKey = 'id_percakapan';

    protected $fillable = [
        'id_pengguna',
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
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function pesan()
    {
        return $this->hasMany(Pesan::class, 'id_percakapan', 'id_percakapan');
    }
}

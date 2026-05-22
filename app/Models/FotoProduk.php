<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoProduk extends Model
{
    protected $table = 'foto_produk';

    protected $primaryKey = 'id_foto_produk';

    protected $fillable = [
        'id_item_produksi',
        'nama_foto',
    ];

    protected $casts = [
        'id_foto_produk' => 'integer',
        'id_item_produksi' => 'integer',
    ];

    // Relasi ke item produksi
    public function itemProduksi()
    {
        return $this->belongsTo(ItemProduksi::class, 'id_item_produksi');
    }
}
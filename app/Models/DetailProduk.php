<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProduk extends Model
{
    protected $table = 'detail_produk';
    protected $primaryKey = 'id_detail_produk';
    public $timestamps = true;
    protected $fillable = [
        'id_item_produksi',
        'id_satuan',
        'ukuran',
        'harga_dasar',
    ];

    public function itemProduksi()
    {
        return $this->belongsTo(ItemProduksi::class, 'id_item_produksi', 'id_item_produksi');
    }
    public function satuanHarga()
    {
        return $this->belongsTo(SatuanHarga::class, 'id_satuan', 'id_satuan');
    }
}

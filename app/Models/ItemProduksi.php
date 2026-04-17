<?php

namespace App\Models;

use App\Models\DetailProduk;
use Illuminate\Database\Eloquent\Model;

class ItemProduksi extends Model
{
    protected $table = 'item_produksi';
    protected $primaryKey = 'id_item_produksi';
    public $timestamps = true;
    protected $fillable = [
        'id_kategori',
        'nama_item',
        'deskripsi_item',
        'gambar_item',
        'status_aktif',
    ];

    public function kategoriUsaha()
    {
        return $this->belongsTo(KategoriUsaha::class, 'id_kategori', 'id_kategori');
    }

    public function detailProduk()
    {
        return $this->hasOne(DetailProduk::class, 'id_item_produksi', 'id_item_produksi');
    }
}

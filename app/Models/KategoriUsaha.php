<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItemProduksi;

class KategoriUsaha extends Model
{
    protected $table = 'kategori_usaha';
    protected $primaryKey = 'id_kategori';
    public $timestamps = true;
    protected $fillable = [
        'id_jenis_pembayaran',
        'kode_unik',
        'nama_kategori',
        'jenis_harga',
        'deskripsi',
    ];

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class, 'id_jenis_pembayaran');
    }

    // mewajibkan kode unik selalu dalam huruf kapital
    public function setKodeUnikAttribute($value): void
    {
        $this->attributes['kode_unik'] = strtoupper($value);
    }

    public function itemProduksi()
    {
        return $this->hasMany(ItemProduksi::class, 'id_kategori', 'id_kategori');
    }
}
 
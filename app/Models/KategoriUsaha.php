<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriUsaha extends Model
{
    protected $table = 'kategori_usaha';
    protected $primaryKey = 'id_kategori';
    public $timestamps = true;
    protected $fillable = [
        'id_jenis_pembayaran',
        'nama_kategori',
        'jenis_harga',
        'deskripsi',
    ];

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class, 'id_jenis_pembayaran');
    }
}

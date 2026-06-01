<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RincianPesanan extends Model
{
    protected $table = 'rincian_pesanan';
    protected $primaryKey = 'id_rincian_pesanan';
    public $timestamps = true;

    protected $fillable = [
        'id_pesanan',
        'id_detail_produk',
        'kuantitas',
        'subtotal',
        'barang_disediakan_usah',
        // 'file_desain',
        'opsi',
    ];

    protected $casts = [
        'kuantitas' => 'integer',
        'subtotal' => 'decimal:2',
        'opsi' => 'array',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function detailProduk()
    {
        return $this->belongsTo(DetailProduk::class, 'id_detail_produk', 'id_detail_produk');
    }
}
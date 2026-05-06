<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $timestamps = true;

    protected $fillable = [
        'id_pengguna',
        'id_detail_produk',
        'id_status_pesanan',
        'tanggal_pesan',
        'nama_penerima',
        'alamat_penerima',
        'No_hp_penerima',
        'total_harga',
    ];

    protected $casts = [
        'tanggal_pesan' => 'date',
        'total_harga' => 'decimal:2',
    ];

    // RELATIONSHIPS
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function statusPesanan()
    {
        return $this->belongsTo(StatusPesanan::class, 'id_status_pesanan', 'id_status_pesanan');
    }

    public function detailProduk()
    {
        return $this->belongsTo(DetailProduk::class, 'id_detail_produk', 'id_detail_produk');
    }

    // public function rincianPesanan()
    // {
    //     return $this->hasMany(RincianPesanan::class, 'id_pesanan', 'id_pesanan');
    // }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }

    // public function persetujuanHarga()
    // {
    //     return $this->hasOne(PersetujuanHarga::class, 'id_pesanan', 'id_pesanan');
    // }/

    // HELPER METHODS

    public function totalDP()
    {
        return $this->pembayaran()
            ->where('tahap_pembayaran', 'DP')
            ->where('status_bayar', 'Lunas')
            ->sum('jumlah_bayar');
    }

    public function totalPelunasan()
    {
        return $this->pembayaran()
            ->where('tahap_pembayaran', 'Pelunasan')
            ->where('status_bayar', 'Lunas')
            ->sum('jumlah_bayar');
    }

    public function sisaBayar()
    {
        $sudahBayar = $this->pembayaran()
            ->where('status_bayar', 'Lunas')
            ->sum('jumlah_bayar');

        return $this->total_harga - $sudahBayar;
    }

    public function sudahLunas()
    {
        return $this->sisaBayar() <= 0;
    }
}

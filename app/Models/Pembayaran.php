<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = true;

    protected $fillable = [
        'id_pesanan',
        'tahap_pembayaran',
        'jumlah_bayar',
        'metode_bayar',
        'payment_type',
        'transaction_id',
        'order_id',
        'bukti_bayar',
        'status_bayar',
        'payload',
        'paid_at',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}

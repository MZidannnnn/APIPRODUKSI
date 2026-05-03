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
        'jumlah_bayar',
        'metode_bayar',
        'payment_type',
        'transaction_id',
        'order_id',
        'bukti_bayar',
        'status_bayar',
        'payload',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'payload' => 'array',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}

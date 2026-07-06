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
        'tipe_pembayaran',
        'jumlah_bayar',
        'payment_type',
        'transaction_id',
        'order_id',
        'bukti_bayar',
        'status_bayar',
        'payload',
        'snap_token',
        'snap_expires_at',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'payload' => 'array',
        'snap_expires_at' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}

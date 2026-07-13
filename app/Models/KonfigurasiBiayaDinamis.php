<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfigurasiBiayaDinamis extends Model
{
    protected $table = 'konfigurasi_biaya_dinamis';
    protected $primaryKey = 'id_konfigurasi';
    public $timestamps = true;

    protected $fillable = [
        'id_item_produksi',
        'is_biaya_jarak_aktif',
        'tarif_per_km',
        'batas_hari_zona_merah',
        'tipe_penentuan_waktu',
        'estimasi_pengerjaan',
    ];

    protected $casts = [
        'is_biaya_jarak_aktif' => 'boolean',
        'tarif_per_km' => 'decimal:2',
    ];

    public function itemProduksi()
    {
        return $this->belongsTo(ItemProduksi::class, 'id_item_produksi', 'id_item_produksi');
    }
}

<?php

namespace App\Observers;

use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PesananObserver
{
    public function created(Pesanan $pesanan): void
    {
        if (!empty($pesanan->kode_resi_pesanan)) {
            return;
        }

        $kodeUnik = DB::table('detail_produk')
            ->join('item_produksi', 'detail_produk.id_item_produksi', '=', 'item_produksi.id_item_produksi')
            ->join('kategori_usaha', 'item_produksi.id_kategori', '=', 'kategori_usaha.id_kategori')
            ->where('detail_produk.id_detail_produk', $pesanan->id_detail_produk)
            ->value('kategori_usaha.kode_unik');

        if (!$kodeUnik) {
            throw new RuntimeException('Kode unik kategori tidak ditemukan.');
        }

        $tanggal = $pesanan->tanggal_pesan->format('Ymd');
        $kodeResi = strtoupper($kodeUnik) . '-' . $tanggal . '-' . $pesanan->id_pesanan;

        $pesanan->updateQuietly(['kode_resi_pesanan' => $kodeResi]);
    }
}

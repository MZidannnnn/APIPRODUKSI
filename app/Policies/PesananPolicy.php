<?php

namespace App\Policies;

use App\Models\Pengguna;
use App\Models\Pesanan;

class PesananPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function updateStatus(Pengguna $user, Pesanan $pesanan): bool
    {
        if ((int) $user->id_role !== 2) {
            return false;
        }

        $pesanan->loadMissing('detailProduk.itemProduksi');

        $kategoriId = $pesanan->detailProduk?->itemProduksi?->id_kategori;

        return $kategoriId !== null
            && (int) $kategoriId === (int) $user->id_kategori;
    }

    public function viewAny(Pengguna $user): bool
    {
        return (int) $user->id_role === 2;
    }
    
    public function cancel(Pengguna $user, Pesanan $pesanan): bool
    {
        return (int) $user->id_role === 3
            && (int) $pesanan->id_pengguna === (int) $user->id_pengguna;
    }
}

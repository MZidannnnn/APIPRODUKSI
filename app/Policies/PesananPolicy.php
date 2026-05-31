<?php

namespace App\Policies;

use App\Models\pengguna;
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

    public function updateStatus(pengguna $user, Pesanan $pesanan): bool
    {
        if ((int) $user->id_role !== 2) {
            return false;
        }

        $pesanan->loadMissing('detailProduk.itemProduksi');

        $kategoriId = $pesanan->detailProduk?->itemProduksi?->id_kategori;

        return $kategoriId !== null
            && (int) $kategoriId === (int) $user->id_kategori;
    }

    public function viewAny(pengguna $user): bool
    {
        return (int) $user->id_role === 2;
    }
    
    public function cancel(pengguna $user, Pesanan $pesanan): bool
    {
        return (int) $user->id_role === 3
            && (int) $pesanan->id_pengguna === (int) $user->id_pengguna;
    }
}

<?php

namespace App\Policies;

use App\Models\pengguna;
use App\Models\PesanLampiran;

class PesanLampiranPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    
    public function view(pengguna $user, PesanLampiran $lampiran): bool
    {
        $percakapan = $lampiran->pesan?->percakapan;

        if (! $percakapan) {
            return false;
        }

        if ((int) $user->id_role === 2) {
            return (int) $user->id_kategori === (int) $percakapan->id_kategori;
        }

        return (int) $percakapan->id_pengguna === (int) $user->id_pengguna;
    }
}

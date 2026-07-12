<?php

namespace App\Policies;

use App\Models\Pengguna;
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
    
    public function view(Pengguna $user, PesanLampiran $lampiran): bool
    {
        $percakapan = $lampiran->pesan?->percakapan;

        if (! $percakapan) {
            return false;
        }

        if ((int) $user->id_role === 2) {
            // Admin (role 2) memiliki akses global — tidak perlu filter id_kategori
            return true;
        }

        return (int) $percakapan->id_pengguna === (int) $user->id_pengguna;
    }
}

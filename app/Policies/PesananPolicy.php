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
        // Admin (role 2) bisa update status semua pesanan tanpa filter kategori
        return (int) $user->id_role === 2;
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

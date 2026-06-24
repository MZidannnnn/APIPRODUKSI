<?php

namespace App\Policies;

use App\Models\Pengguna;
use App\Models\Percakapan;

class PercakapanPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function accessAdmin(Pengguna $user, Percakapan $percakapan): bool
    {
        return (int) $user->id_role === 2
            && (int) $user->id_kategori === (int) $percakapan->id_kategori;
    }
}

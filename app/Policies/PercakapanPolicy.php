<?php

namespace App\Policies;

use App\Models\pengguna;
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

    public function accessAdmin(pengguna $user, Percakapan $percakapan): bool
    {
        return (int) $user->id_role === 2
            && (int) $user->id_kategori === (int) $percakapan->id_kategori;
    }
}

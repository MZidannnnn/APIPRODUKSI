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
        // Admin (role 2) bisa mengakses semua percakapan tanpa filter kategori
        return (int) $user->id_role === 2;
    }
}

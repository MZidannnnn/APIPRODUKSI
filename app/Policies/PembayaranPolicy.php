<?php

namespace App\Policies;

use App\Models\Pengguna;

class PembayaranPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAdminHistory(Pengguna $user): bool
    {
        return (int) $user->id_role === 2;
    }
}

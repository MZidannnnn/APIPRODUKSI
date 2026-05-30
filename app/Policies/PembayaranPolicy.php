<?php

namespace App\Policies;

use App\Models\pengguna;

class PembayaranPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAdminHistory(pengguna $user): bool
    {
        return (int) $user->id_role === 2;
    }
}

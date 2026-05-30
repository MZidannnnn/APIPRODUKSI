<?php

namespace App\Policies;

use App\Models\ItemProduksi;
use App\Models\pengguna;

class ItemProduksiPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function viewAny(pengguna $user): bool
    {
        return (int) $user->id_role === 2;
    }
    public function view(pengguna $user, ItemProduksi $itemProduksi): bool
    {
        return (int) $user->id_role === 2;
    }

    public function create(pengguna $user): bool
    {
        return (int) $user->id_role === 2;
    }

    public function update(pengguna $user, ItemProduksi $itemProduksi): bool
    {
        return (int) $user->id_role === 2;
    }

    public function delete(pengguna $user, ItemProduksi $itemProduksi): bool
    {
        return (int) $user->id_role === 2;
    }
}

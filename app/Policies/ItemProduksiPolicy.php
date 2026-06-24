<?php

namespace App\Policies;

use App\Models\ItemProduksi;
use App\Models\Pengguna;

class ItemProduksiPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function viewAny(Pengguna $user): bool
    {
        return (int) $user->id_role === 2;
    }
    public function view(Pengguna $user, ItemProduksi $itemProduksi): bool
    {
        return (int) $user->id_role === 2;
    }

    public function create(Pengguna $user): bool
    {
        return (int) $user->id_role === 2;
    }

    public function update(Pengguna $user, ItemProduksi $itemProduksi): bool
    {
        return (int) $user->id_role === 2;
    }

    public function delete(Pengguna $user, ItemProduksi $itemProduksi): bool
    {
        return (int) $user->id_role === 2;
    }
}

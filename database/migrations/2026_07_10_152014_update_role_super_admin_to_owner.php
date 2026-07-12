<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrasi data role:
     * Mengubah nama role 'super admin' (id=1) menjadi 'owner'.
     * Role 'admin' (id=2) dan 'klien' (id=3) tidak berubah.
     */
    public function up(): void
    {
        DB::table('role')
            ->where('id_role', 1)
            ->update(['nama_role' => 'owner']);
    }

    /**
     * Rollback: kembalikan nama role 'owner' menjadi 'super admin'.
     */
    public function down(): void
    {
        DB::table('role')
            ->where('id_role', 1)
            ->update(['nama_role' => 'super admin']);
    }
};

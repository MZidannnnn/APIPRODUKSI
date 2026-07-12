<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Owner
        Pengguna::create([
            'id_role'       => 1,
            'nama_pengguna' => 'Owner',
            'email'         => 'owner@gmail.com',
            'password'      => 'owner12345',
        ]);

        // Admin
        Pengguna::create([
            'id_role'       => 2,
            'nama_pengguna' => 'Admin Sistem',
            'email'         => 'admin@gmail.com',
            'password'      => 'admin123',
        ]);

        // Klien
        Pengguna::create([
            'id_role'       => 3,
            'nama_pengguna' => 'User Demo',
            'email'         => 'user@gmail.com',
            'password'      => 'user12345',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('jenis_pembayaran')->insert([
            ['nama_jenis_pembayaran' => 'Transfer Bank', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis_pembayaran' => 'VA', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis_pembayaran' => 'QRIS', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

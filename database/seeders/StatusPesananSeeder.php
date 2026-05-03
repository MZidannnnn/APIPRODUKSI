<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status_pesanan')->insert([
            ['nama_status_pesanan' => 'Menunggu Pembayaran', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status_pesanan' => 'Diproses', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status_pesanan' => 'Selesai', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status_pesanan' => 'Dibatalkan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

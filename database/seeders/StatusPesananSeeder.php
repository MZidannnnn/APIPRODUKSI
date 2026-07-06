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
            ['nama_status_pesanan' => 'Belum Bayar', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status_pesanan' => 'Pesanan Diproses', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status_pesanan' => 'Pesanan selesai, silahkan lunasi pembayaran', 'created_at' => now(), 'updated_at' => now()],
            // ['nama_status_pesanan' => 'Menunggu Pelunasan', 'created_at' => now(), 'updated_at' => now()], // Status baru untuk skema DP
            ['nama_status_pesanan' => 'Pesanan Selesai', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status_pesanan' => 'Pesanan Dibatalkan', 'created_at' => now(), 'updated_at' => now()],
            //['nama_status_pesanan' => 'Pesanan Kadaluarsa', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

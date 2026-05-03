<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanHargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('satuan_harga')->insert([
            ['nama_satuan' => 'pcs', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'meter', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'paket', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

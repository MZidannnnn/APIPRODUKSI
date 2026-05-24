<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_produk')->insert([
            [
                'id_item_produksi' => 1,
                'ukuran' => '1x2',
                'harga_dasar' => 150000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_item_produksi' => 2,
                'ukuran' => 'Paket',
                'harga_dasar' => 500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

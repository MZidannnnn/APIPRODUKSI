<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemProduksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('item_produksi')->insert([
            [
                'id_kategori' => 1,
                'nama_item' => 'Banner 1x2',
                'deskripsi_item' => 'Banner ukuran 1x2 meter',
                'gambar_item' => null,
                'status_aktif' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori' => 2,
                'nama_item' => 'Desain Logo Premium',
                'deskripsi_item' => 'Desain logo dengan revisi',
                'gambar_item' => null,
                'status_aktif' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

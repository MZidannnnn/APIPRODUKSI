<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriUsahaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori_usaha')->insert([
            [
                'id_jenis_pembayaran' => 1,
                'nama_kategori' => 'Cetak Banner',
                'jenis_harga' => 'Harga Tetap',
                'deskripsi' => 'Cetak banner dan spanduk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_jenis_pembayaran' => 2,
                'nama_kategori' => 'Desain Logo',
                'jenis_harga' => 'Harga Kostum',
                'deskripsi' => 'Jasa desain logo custom',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

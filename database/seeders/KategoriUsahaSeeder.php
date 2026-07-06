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
                'kode_unik' => 'CTB',
                'nama_kategori' => 'Space Iklan Baliho',
                'bidang_layanan' => 'Media Promosi',
                'deskripsi' => 'Cetak banner dan spanduk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_jenis_pembayaran' => 2,
                'kode_unik' => 'INT',
                'nama_kategori' => 'Interior',
                'bidang_layanan' => 'Produksi',
                'deskripsi' => 'Kungkungan interior, dekorasi, dan furniture custom',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_jenis_pembayaran' => 2,
                'kode_unik' => 'SBL',
                'nama_kategori' => 'Sablon',
                'bidang_layanan' => 'Produksi',
                'deskripsi' => 'Jasa sablon custom',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

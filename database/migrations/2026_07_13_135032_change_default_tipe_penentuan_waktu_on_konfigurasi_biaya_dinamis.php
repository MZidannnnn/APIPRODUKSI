<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('konfigurasi_biaya_dinamis', function (Blueprint $table) {
            $table->enum('tipe_penentuan_waktu', ['tanggal', 'estimasi'])->default('estimasi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konfigurasi_biaya_dinamis', function (Blueprint $table) {
            $table->enum('tipe_penentuan_waktu', ['tanggal', 'estimasi'])->default('tanggal')->change();
        });
    }
};

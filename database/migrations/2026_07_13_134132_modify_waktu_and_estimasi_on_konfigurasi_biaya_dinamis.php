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
            $table->dropColumn(['is_biaya_waktu_aktif', 'batas_hari_zona_kuning', 'biaya_urgensi']);
            $table->enum('tipe_penentuan_waktu', ['tanggal', 'estimasi'])->default('tanggal')->after('batas_hari_zona_merah');
            $table->string('estimasi_pengerjaan')->nullable()->after('tipe_penentuan_waktu');
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('total_biaya_waktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konfigurasi_biaya_dinamis', function (Blueprint $table) {
            $table->boolean('is_biaya_waktu_aktif')->default(false);
            $table->integer('batas_hari_zona_kuning')->nullable();
            $table->decimal('biaya_urgensi', 14, 2)->nullable();
            $table->dropColumn(['tipe_penentuan_waktu', 'estimasi_pengerjaan']);
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('total_biaya_waktu', 14, 2)->default(0)->after('total_biaya_jarak');
        });
    }
};

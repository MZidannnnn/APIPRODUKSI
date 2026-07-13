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
            $table->integer('batas_hari_zona_merah')->nullable()->after('is_biaya_waktu_aktif');
            $table->renameColumn('minimal_hari_tenggat', 'batas_hari_zona_kuning');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konfigurasi_biaya_dinamis', function (Blueprint $table) {
            $table->renameColumn('batas_hari_zona_kuning', 'minimal_hari_tenggat');
            $table->dropColumn('batas_hari_zona_merah');
        });
    }
};

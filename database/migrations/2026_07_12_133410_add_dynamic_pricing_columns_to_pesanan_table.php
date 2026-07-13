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
        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat_penerima');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->text('alamat_lengkap')->nullable()->after('longitude');
            $table->decimal('total_biaya_jarak', 14, 2)->default(0)->after('total_harga');
            $table->decimal('total_biaya_waktu', 14, 2)->default(0)->after('total_biaya_jarak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'alamat_lengkap',
                'total_biaya_jarak',
                'total_biaya_waktu'
            ]);
        });
    }
};

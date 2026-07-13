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
        Schema::create('konfigurasi_biaya_dinamis', function (Blueprint $table) {
            $table->bigIncrements('id_konfigurasi');
            $table->unsignedBigInteger('id_item_produksi');
            $table->boolean('is_biaya_jarak_aktif')->default(false);
            $table->decimal('tarif_per_km', 14, 2)->nullable();
            $table->boolean('is_biaya_waktu_aktif')->default(false);
            $table->integer('minimal_hari_tenggat')->nullable();
            $table->decimal('biaya_urgensi', 14, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_item_produksi')
                ->references('id_item_produksi')
                ->on('item_produksi')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_biaya_dinamis');
    }
};

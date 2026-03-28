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
        Schema::create('item_produksi', function (Blueprint $table) {
            $table->bigIncrements('id_item_produksi');
            $table->unsignedBigInteger('id_kategori');
            $table->string('nama_item', 100);
            $table->text('deskripsi_item')->nullable();
            $table->string('gambar_item', 255)->nullable();
            $table->enum('status_aktif', ['Aktif', 'Non-aktif']);
            $table->timestamps();

            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori_usaha')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_produksi');
    }
};

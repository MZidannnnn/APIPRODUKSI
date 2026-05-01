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
        Schema::create('kategori_usaha', function (Blueprint $table) {
            $table->bigIncrements('id_kategori');
            $table->unsignedBigInteger('id_jenis_pembayaran');
            $table->string('nama_kategori', 100);
            $table->enum('jenis_harga', ['Harga Tetap', 'Harga Kostum']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('id_jenis_pembayaran')
                ->references('id_jenis_pembayaran')
                ->on('jenis_pembayaran')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_usaha');
    }
};

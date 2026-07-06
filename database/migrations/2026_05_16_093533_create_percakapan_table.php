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
        Schema::create('percakapan', function (Blueprint $table) {
            $table->bigIncrements('id_percakapan');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_item_produksi')->nullable();
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->timestamp('terakhir_aktif')->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_item_produksi')
                ->references('id_item_produksi')->on('item_produksi')
                ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('id_kategori')
                ->references('id_kategori')->on('kategori_usaha')
                ->onDelete('restrict')->onUpdate('cascade');

            $table->index(['id_pengguna', 'id_item_produksi']);
            $table->index(['id_kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percakapan');
    }
};

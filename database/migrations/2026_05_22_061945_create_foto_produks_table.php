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
        Schema::create('foto_produk', function (Blueprint $table) {
            $table->bigIncrements('id_foto_produk');
            $table->unsignedBigInteger('id_item_produksi');
            $table->string('nama_foto', 255);
            $table->timestamps();

            // Foreign Key
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
        Schema::dropIfExists('foto_produks');
    }
};

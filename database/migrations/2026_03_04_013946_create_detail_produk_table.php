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
        Schema::create('detail_produk', function (Blueprint $table) {
            $table->bigIncrements('id_detail_produk');
            $table->unsignedBigInteger('id_item_produksi');
            $table->unsignedBigInteger('id_satuan');
            $table->string('ukuran', 50);
            $table->decimal('harga_dasar', 14, 2);
            $table->timestamps();

            $table->foreign('id_item_produksi')
                ->references('id_item_produksi')
                ->on('item_produksi')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_satuan')
                ->references('id_satuan')
                ->on('satuan_harga')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_produk');
    }
};

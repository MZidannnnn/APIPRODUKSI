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
        Schema::create('rincian_pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_rincian_pesanan');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_detail_produk');
            $table->integer('kuantitas');
            $table->decimal('subtotal', 14, 2);
            $table->enum('barang_disediakan_usah', ['Ya', 'Tidak']);
            $table->string('file_desain', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('pesanan')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_detail_produk')
                ->references('id_detail_produk')
                ->on('detail_produk')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_pesanan');
    }
};

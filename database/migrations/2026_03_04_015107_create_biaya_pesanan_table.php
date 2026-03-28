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
        Schema::create('biaya_pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_biaya');
            $table->unsignedBigInteger('id_rincian_pesanan');
            $table->string('nama_biaya', 100);
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 14, 2);
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();

            $table->foreign('id_rincian_pesanan')
                ->references('id_rincian_pesanan')
                ->on('rincian_pesanan')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_pesanan');
    }
};

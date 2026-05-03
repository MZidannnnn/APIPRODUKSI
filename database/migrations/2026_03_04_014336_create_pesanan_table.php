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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_pesanan');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_detail_produk');
            $table->unsignedBigInteger('id_status_pesanan');
            $table->date('tanggal_pesan');
            $table->string('nama_penerima', 100);
            $table->text('alamat_penerima');
            $table->string('No_hp_penerima', 20);
            $table->decimal('total_harga', 14, 2);
            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_status_pesanan')
                ->references('id_status_pesanan')
                ->on('status_pesanan')
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
        Schema::dropIfExists('pesanan');
    }
};

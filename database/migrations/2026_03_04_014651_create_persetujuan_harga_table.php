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
        Schema::create('persetujuan_harga', function (Blueprint $table) {
            $table->bigIncrements('id_persetujuan');
            $table->unsignedBigInteger('id_pesanan');
            $table->enum('status_persetujuan', ['Disetujui', 'Ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('pesanan')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan_harga');
    }
};

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
            $table->unsignedBigInteger('id_pesanan')->nullable();
            $table->timestamp('terakhir_aktif')->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('pesanan')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->index(['id_pengguna', 'id_pesanan']);
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

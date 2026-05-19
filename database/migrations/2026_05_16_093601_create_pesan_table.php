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
        Schema::create('pesan', function (Blueprint $table) {
            $table->bigIncrements('id_pesan');
            $table->unsignedBigInteger('id_percakapan');
            $table->unsignedBigInteger('id_pengirim');
            $table->text('isi_pesan')->nullable();
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->foreign('id_percakapan')
                ->references('id_percakapan')
                ->on('percakapan')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_pengirim')
                ->references('id_pengguna')
                ->on('pengguna')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->index(['id_percakapan', 'id_pesan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan');
    }
};

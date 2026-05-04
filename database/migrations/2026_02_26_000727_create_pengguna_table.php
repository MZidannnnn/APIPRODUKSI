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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->bigIncrements('id_pengguna');
            $table->unsignedBigInteger('id_role');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('nama_pengguna', 100);
            $table->string('email', 150);
            $table->string('password', 255);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_role')
                ->references('id_role')
                ->on('role')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori_usaha')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};

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
        Schema::create('pesan_lampiran', function (Blueprint $table) {
            $table->bigIncrements('id_lampiran');
            $table->unsignedBigInteger('id_pesan');
            $table->string('jenis', 20);
            $table->string('disk', 50);
            $table->string('path', 2048);
            $table->string('original_name', 255);
            $table->string('stored_name', 255);
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->char('checksum', 64);
            $table->timestamps();

            $table->foreign('id_pesan')
                ->references('id_pesan')->on('pesan')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->index(['id_pesan', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan_lampiran');
    }
};

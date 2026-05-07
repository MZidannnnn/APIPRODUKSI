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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->bigIncrements('id_pembayaran');
            $table->unsignedBigInteger('id_pesanan');
            $table->enum('tahap_pembayaran', ['DP', 'Pelunasan'])->default('Pelunasan');
            $table->decimal('jumlah_bayar', 14, 2);
            $table->string('metode_bayar', 50)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->string('order_id', 100)->nullable();
            $table->string('bukti_bayar', 255)->nullable();
            $table->enum('status_bayar', ['Pending', 'Lunas']);
            $table->timestamp('paid_at')->nullable()->after('status_bayar');
            $table->json('payload')->nullable(); 
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
        Schema::dropIfExists('pembayaran');
    }
};

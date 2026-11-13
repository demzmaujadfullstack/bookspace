<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // restrict: riwayat transaksi tidak boleh hilang saat user dihapus
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('code', 30)->unique();
            $table->enum('status', [
                'pending',
                'menunggu_pembayaran',
                'menunggu_verifikasi',
                'dibayar',
                'diproses',
                'dikirim',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_pembayaran');
            // snapshot alamat saat checkout agar riwayat tidak berubah bila alamat diedit
            $table->string('recipient_name', 100);
            $table->string('phone', 20);
            $table->text('address');
            $table->unsignedInteger('total');
            $table->string('note', 300)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

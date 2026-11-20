<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['transfer', 'qris']);
            $table->unsignedInteger('amount');
            $table->enum('status', ['menunggu', 'menunggu_verifikasi', 'diterima', 'ditolak'])->default('menunggu');
            $table->string('proof_image')->nullable();
            // snapshot rekening tujuan saat checkout (pengaturan admin bisa berubah nanti)
            $table->string('bank_name', 60)->nullable();
            $table->string('account_number', 60)->nullable();
            $table->string('account_holder', 80)->nullable();
            $table->string('note', 300)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

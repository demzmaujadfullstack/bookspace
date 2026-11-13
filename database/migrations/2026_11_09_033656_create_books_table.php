<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            // restrict: kategori yang masih punya buku tidak boleh dihapus
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('author', 120);
            $table->string('publisher', 120)->nullable();
            $table->string('isbn', 20)->nullable()->unique();
            $table->unsignedSmallInteger('year')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->string('cover')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['category_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};

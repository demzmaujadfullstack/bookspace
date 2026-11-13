<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->index('stock');
            $table->index('discount_percent');
            $table->index(['is_featured', 'stock']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['book_id', 'rating']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['stock']);
            $table->dropIndex(['discount_percent']);
            $table->dropIndex(['is_featured', 'stock']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['book_id', 'rating']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });
    }
};

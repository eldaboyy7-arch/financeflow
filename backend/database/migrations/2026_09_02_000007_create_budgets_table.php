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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->unsignedTinyInteger('month'); // 1 - 12
            $table->unsignedSmallInteger('year');  // e.g. 2026
            $table->timestamps();

            // Mencegah duplikasi budget untuk kategori yang sama pada bulan & tahun yang sama
            $table->unique(['user_id', 'category_id', 'year', 'month'], 'unique_user_cat_year_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};

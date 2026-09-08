<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->string('badge')->nullable();
            $table->string('badge_color')->default('blue');
            $table->decimal('price', 15, 2);
            $table->string('price_label')->default('HARGA MULAI');
            $table->string('duration')->default('Full Day Tour (8 - 10 Jam)');
            $table->string('capacity')->default('15 Person');
            $table->string('vehicle_name')->nullable();
            $table->text('description')->nullable();
            $table->text('tour_route')->nullable();
            $table->string('cover_photo_path', 500)->nullable();
            $table->json('gallery_photos')->nullable();
            $table->json('facilities')->nullable();
            $table->json('itinerary')->nullable();
            $table->json('included')->nullable();
            $table->json('excluded')->nullable();
            $table->text('cta_whatsapp_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};

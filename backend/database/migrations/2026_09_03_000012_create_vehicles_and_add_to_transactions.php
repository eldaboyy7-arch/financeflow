<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Toyota Avanza"
            $table->string('plate_number')->nullable(); // e.g. "B 1234 XYZ"
            $table->string('brand')->nullable(); // e.g. "Toyota"
            $table->string('model_year')->nullable(); // e.g. "2022"
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available');
            $table->decimal('daily_rate', 15, 2)->default(0); // Tarif sewa/hari
            $table->string('color', 7)->default('#3B82F6');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete()->after('category_id');
        });
    }

    public function down(): void {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn('vehicle_id');
        });
        Schema::dropIfExists('vehicles');
    }
};

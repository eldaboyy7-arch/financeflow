<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('status');
            $table->string('video_url', 500)->nullable()->after('photo_path');
            $table->string('video_path')->nullable()->after('video_url');
            $table->enum('transmission', ['matic', 'manual'])->default('matic')->after('video_path');
            $table->unsignedSmallInteger('capacity')->default(7)->after('transmission');
            $table->enum('fuel_type', ['bensin', 'diesel'])->default('bensin')->after('capacity');
            $table->text('description')->nullable()->after('fuel_type');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'photo_path',
                'video_url',
                'video_path',
                'transmission',
                'capacity',
                'fuel_type',
                'description',
            ]);
        });
    }
};

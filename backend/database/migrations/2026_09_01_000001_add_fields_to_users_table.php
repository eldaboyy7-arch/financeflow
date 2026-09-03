<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Note: ->after() is MySQL-only and has no effect on PostgreSQL.
            // Columns are added at the end in PostgreSQL — this is intentional.
            $table->string('avatar')->nullable();
            $table->string('currency', 3)->default('IDR');
            $table->unsignedBigInteger('default_account_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'currency', 'default_account_id']);
        });
    }
};

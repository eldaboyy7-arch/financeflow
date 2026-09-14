<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            // UNIQUE constraint on twin_id — allows multiple NULLs (PostgreSQL semantics:
            // NULL != NULL, so multiple NULLs are permitted in a unique index).
            // This means:
            //   - Two different rows CANNOT point to the same twin_id (prevents duplicate twins)
            //   - A row CANNOT point to itself (circular reference, as long as IDs differ)
            //   - Rows without a twin yet can stay NULL with no constraint violation
            $table->unique('twin_id');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropUnique(['twin_id']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            // Stores the ID of the mirrored twin row in the other user's account.
            // This gives the sync hook a stable, unambiguous reference to find the
            // correct twin — independent of slug conventions or title matching.
            $table->unsignedBigInteger('twin_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn('twin_id');
        });
    }
};

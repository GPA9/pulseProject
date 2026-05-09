<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add total_plays to musician_profiles for cached top artists
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('total_plays')->default(0)->after('image_path');
        });

        // Add coordinates to concerts for Leaflet map
        Schema::table('concerts', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('capacity_available');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->dropColumn('total_plays');
        });

        Schema::table('concerts', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
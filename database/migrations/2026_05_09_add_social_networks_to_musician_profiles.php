<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->json('social_networks')->nullable()->after('image_path');
            $table->json('streaming_platforms')->nullable()->after('social_networks');
        });
    }

    public function down(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->dropColumn(['social_networks', 'streaming_platforms']);
        });
    }
};

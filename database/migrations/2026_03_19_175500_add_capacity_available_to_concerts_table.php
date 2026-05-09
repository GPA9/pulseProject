<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('concerts', function (Blueprint $table) {
            $table->unsignedInteger('capacity_available')->nullable()->after('capacity');
        });

        // Initialize capacity_available = capacity for all existing concerts
        DB::statement('UPDATE concerts SET capacity_available = capacity WHERE capacity IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('concerts', function (Blueprint $table) {
            $table->dropColumn('capacity_available');
        });
    }
};

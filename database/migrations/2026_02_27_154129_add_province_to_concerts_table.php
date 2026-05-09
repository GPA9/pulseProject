<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('concerts', function (Blueprint $table) {
            $table->string('province')->nullable()->after('city');
            $table->string('autonomous_community')->nullable()->after('province');
            $table->string('description')->nullable()->after('autonomous_community');
            $table->integer('capacity')->nullable()->after('description');
            $table->string('genre')->nullable()->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('concerts', function (Blueprint $table) {
            $table->dropColumn(['province', 'autonomous_community', 'description', 'capacity', 'genre']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->string('province')->nullable()->after('city');
            $table->string('autonomous_community')->nullable()->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('musician_profiles', function (Blueprint $table) {
            $table->dropColumn(['province', 'autonomous_community']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('merches', function (Blueprint $table) {
            $table->string('category')->default('Otros')->after('image_path');
            $table->string('city')->nullable()->after('category');
            $table->unsignedInteger('sales_count')->default(0)->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('merches', function (Blueprint $table) {
            $table->dropColumn(['category', 'city', 'sales_count']);
        });
    }
};

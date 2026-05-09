<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('merches', function (Blueprint $table) {
            $table->json('sizes')->nullable()->comment('Available sizes: XS, S, M, L, XL, XXL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merches', function (Blueprint $table) {
            $table->dropColumn('sizes');
        });
    }
};

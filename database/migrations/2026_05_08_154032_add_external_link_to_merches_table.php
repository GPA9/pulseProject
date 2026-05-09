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
            $table->string('merchbar_url')->nullable()->comment('URL externa de Merchbar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merches', function (Blueprint $table) {
            //
        });
    }
};

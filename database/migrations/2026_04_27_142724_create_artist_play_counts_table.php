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
        Schema::create('artist_play_counts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musician_profile_id')->constrained()->onDelete('cascade');
            $table->integer('play_count')->default(0);
            $table->date('recorded_date')->unique('artist_play_counts_unique_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_play_counts');
    }
};

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
        Schema::create('song_play_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->timestamp('played_at')->useCurrent();
            $table->index('played_at');
            $table->index('song_id');
        });

        // Tabla para rankings en caché
        Schema::create('musician_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musician_profile_id')->constrained()->onDelete('cascade');
            $table->integer('total_plays')->default(0);
            $table->integer('rank')->default(0);
            $table->timestamp('calculated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique('musician_profile_id');
            $table->index('rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musician_rankings');
        Schema::dropIfExists('song_play_logs');
    }
};

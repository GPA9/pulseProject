<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musician_profile_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('cover_path')->nullable();
            $table->text('description')->nullable();
            $table->year('release_year')->nullable();
            $table->timestamps();
        });

        // Add album_id column to songs
        Schema::table('songs', function (Blueprint $table) {
            $table->foreignId('album_id')->nullable()->after('musician_profile_id')
                ->constrained('albums')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropForeign(['album_id']);
            $table->dropColumn('album_id');
        });
        Schema::dropIfExists('albums');
    }
};

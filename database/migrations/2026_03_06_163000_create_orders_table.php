<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('item_type');           // 'concert' | 'merch'
            $table->unsignedBigInteger('item_id');
            $table->string('item_name');           // nombre del artículo (snapshot)
            $table->decimal('amount', 10, 2);      // precio total en €
            $table->decimal('commission', 10, 2);  // 5% Pulse
            $table->decimal('musician_earnings', 10, 2); // 95%
            $table->string('stripe_session_id')->unique()->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

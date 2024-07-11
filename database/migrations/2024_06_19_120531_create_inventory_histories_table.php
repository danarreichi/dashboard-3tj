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
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->foreignId('inventory_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('status'); //in and out only
            $table->decimal('qty', 10, 2);
            $table->decimal('price', 17, 2);
            $table->json('payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};

<?php

use App\Models\MenuRecipe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menu_recipes', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->index();
        });
        MenuRecipe::where('uuid', '')->each(fn ($menuRecipe) => $menuRecipe->update(['uuid' => Str::uuid()->toString()]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_recipes', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};

<?php

use App\Models\InventoryHistory;
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
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->index();
        });
        InventoryHistory::where('uuid', '')->each(fn ($inventoryHistory) => $inventoryHistory->update(['uuid' => Str::uuid()->toString()]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};

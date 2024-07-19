<?php

use App\Models\SaleGroup;
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
        Schema::table('sale_groups', function (Blueprint $table) {
            $table->decimal('discount', 17, 2)->default(0)->after('total');
            $table->decimal('total_after_discount', 17, 2)->after('discount');
        });

        SaleGroup::chunk(50, function($sales){
            $sales->each(function($sale){
                $sale->update(['total_after_discount' => ($sale->total - $sale->discount)]);
            });
        });

        Schema::dropIfExists('discounts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_groups', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};

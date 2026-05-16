<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends BaseModel
{
    use HasFactory;

    public function price()
    {
        return $this->belongsTo(MenuPrice::class, 'menu_price_id');
    }

    public function saleGroup()
    {
        return $this->belongsTo(SaleGroup::class);
    }

    public function scopeStartBetween(Builder $query, $startDate, $endDate): Builder
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        return $query
            ->where('sales.created_at', '>=', $start)
            ->where('sales.created_at', '<=', $end);
    }
}

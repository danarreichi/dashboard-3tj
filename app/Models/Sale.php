<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function scopeStartBetween(Builder $query, $startDate, $endDate)
    {
        return $query
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate);
    }
}

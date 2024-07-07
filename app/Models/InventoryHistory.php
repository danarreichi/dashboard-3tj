<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends BaseModel
{
    use HasFactory;

    public const STATUS_IN = 'in';
    public const STATUS_OUT = 'out';

    protected $casts = [
        'payload' => 'json',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipes()
    {
        return $this->hasMany(MenuRecipe::class);
    }

    public function scopeStartBetween(Builder $query, $startDate, $endDate)
    {
        return $query
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);
    }
}

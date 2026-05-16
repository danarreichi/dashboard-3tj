<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InventoryHistory extends BaseModel
{
    use HasFactory;

    public const STATUS_IN = 'in';
    public const STATUS_OUT = 'out';
    public const IS_CUSTOM = true;

    protected $casts = [
        'payload' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

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
        $start = Carbon::parse($startDate, 'Asia/Jakarta')->startOfDay()->utc();
        $end = Carbon::parse($endDate, 'Asia/Jakarta')->endOfDay()->utc();

        return $query
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);
    }
}

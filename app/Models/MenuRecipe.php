<?php

namespace App\Models;

use App\Traits\RelationShortcut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MenuRecipe extends BaseModel
{
    use HasFactory, RelationShortcut;

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

    public function history()
    {
        return $this->belongsTo(InventoryHistory::class, 'inventory_history_id');
    }

    public function price()
    {
        return $this->belongsTo(MenuPrice::class, 'menu_price_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inventory extends BaseModel
{
    use HasFactory, SoftDeletes;

    public const REDUCIBLE = 'reducible';
    public const FIXED = 'fixed';

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

    public function histories()
    {
        return $this->hasMany(InventoryHistory::class);
    }
}

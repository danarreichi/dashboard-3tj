<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Menu extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $with = ['image', 'category'];

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

    public function image()
    {
        return $this->morphOne(Mediafile::class, 'model')->orderBy('sequence', 'asc');
    }

    public function prices()
    {
        return $this->hasMany(MenuPrice::class);
    }

    public function sales()
    {
        return $this->hasManyThrough(Sale::class, MenuPrice::class);
    }

    public function price()
    {
        return $this->hasOne(MenuPrice::class)->where('status', 'active');
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }
}

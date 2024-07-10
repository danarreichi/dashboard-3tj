<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class MenuPrice extends BaseModel
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
            $model->revision = ($model->where('menu_id', $model->menu_id)->max('revision') ?? 0)  + 1;
            $model->status = 'inactive';
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function recipes()
    {
        return $this->hasMany(MenuRecipe::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Discount extends BaseModel
{
    use HasFactory, SoftDeletes;

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
}
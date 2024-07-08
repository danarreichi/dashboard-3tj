<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Mediafile extends BaseModel
{
    use HasFactory;

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
            if ($model->sequence === null) $model->sequence = 0;
        });
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function getPathAttribute($value)
    {
        return "storage/" . $value;
    }
}

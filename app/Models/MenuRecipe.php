<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuRecipe extends BaseModel
{
    use HasFactory;

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function price()
    {
        return $this->belongsTo(MenuPrice::class);
    }
}

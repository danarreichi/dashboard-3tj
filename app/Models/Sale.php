<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends BaseModel
{
    use HasFactory;

    public function recipe()
    {
        return $this->belongsTo(MenuRecipe::class);
    }
}

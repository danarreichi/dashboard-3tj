<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuRecipe extends BaseModel
{
    use HasFactory;

    public function history()
    {
        return $this->belongsTo(InventoryHistory::class);
    }

    public function price()
    {
        return $this->belongsTo(MenuPrice::class);
    }
}

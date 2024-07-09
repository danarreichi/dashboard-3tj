<?php

namespace App\Models;

use App\Traits\RelationShortcut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuRecipe extends BaseModel
{
    use HasFactory, RelationShortcut;

    public function history()
    {
        return $this->belongsTo(InventoryHistory::class, 'inventory_history_id');
    }

    public function price()
    {
        return $this->belongsTo(MenuPrice::class);
    }
}

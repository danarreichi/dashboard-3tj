<?php

namespace App\Repositories\MenuPrice;

use App\Models\InventoryHistory;
use App\Models\Menu;
use App\Models\MenuPrice;
use App\Models\MenuRecipe;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;

class MenuPriceRepository extends BaseRepository
{
    public function __construct(MenuPrice $model)
    {
        $this->model = $model;
    }

    public function listByMenu(Menu $menu)
    {
        $filters = [AllowedFilter::trashed()];
        $sorts = ['name', 'updated_at'];
        $query = parent::index($filters, $sorts)
            ->where('menu_id', $menu->id)
            ->with(['recipes.history.inventory'])
            ->orderByDesc('id');
        if (request('q')) {
            $query->where(function ($q) {
                $q->where('price', 'LIKE', '%' . request('q') . '%');
                $q->orWhere('status', 'LIKE', '%' . request('q') . '%');
            });
        }
        return $query->paginate(request('limit', 15))->withQueryString();
    }

    public function insertPriceAndRecipes(Menu $menu, array $attributes): Model
    {
        $price = $attributes['price'];
        $menuPrice = parent::create([
            'price' => $price,
            'menu_id' => $menu->id
        ]);
        $recipes = $attributes['recipes'];
        $recipesInsert = collect($recipes)->map(function ($recipe) use ($menuPrice) {
            $recipe['menu_price_id'] = $menuPrice->id;
            $recipe['inventory_history_id'] = InventoryHistory::where('uuid', $recipe['uuid'])->first()->id;
            $recipe['qty'] = $recipe['qty'];
            return $recipe;
        })->values()->toArray();
        MenuRecipe::insert($recipesInsert);
        return $menuPrice;
    }

    public function activatePrice(Menu $menu, MenuPrice $price)
    {
        parent::index()->whereHas('menu', fn ($q) => $q->where('id', $menu->id))->update(['status' => 'inactive']);
        $data = $price->update(['status' => 'active']);
        return $data;
    }
}

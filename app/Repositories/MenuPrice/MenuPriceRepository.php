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
        })->values();
        $recipesInsert->each(function ($recipe) {
            MenuRecipe::create($recipe);
        });
        return $menuPrice;
    }

    public function listActivePrice()
    {
        $data = parent::index()
            ->whereHas('menu', function ($q) {
                $q->when(request('q'), fn ($q) => $q->where('name', 'LIKE', '%' . request('q') . '%'));
                $q->when(request('category_uuid'), function ($q) {
                    $q->whereHas('category', fn ($q) => $q->where('uuid', request('category_uuid')));
                });
            })->with('menu')->where('status', 'active')
            ->addSelect([
                'stock_remaining' => MenuRecipe::selectRaw('FLOOR(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)))')
                    ->join('inventory_histories', 'menu_recipes.inventory_history_id', '=', 'inventory_histories.id')
                    ->join('inventories', 'inventory_histories.inventory_id', '=', 'inventories.id')
                    ->whereColumn('menu_prices.id', 'menu_recipes.menu_price_id')
                    ->limit(1),
                'availability' => MenuRecipe::selectRaw('CASE WHEN FLOOR(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0))) > 0 THEN true ELSE false END')
                    ->join('inventory_histories', 'menu_recipes.inventory_history_id', '=', 'inventory_histories.id')
                    ->join('inventories', 'inventory_histories.inventory_id', '=', 'inventories.id')
                    ->whereColumn('menu_prices.id', 'menu_recipes.menu_price_id')
                    ->limit(1)
            ])
            ->get();
        return $data;
    }

    public function listActivePriceTemp(array $attributes, $param)
    {
        $data = parent::index()
            ->whereHas('menu')
            ->with(['menu', 'recipes.history.inventory'])
            ->where('status', 'active')
            ->addSelect([
                'stock_remaining' => MenuRecipe::selectRaw('FLOOR(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)))')
                    ->join('inventory_histories', 'menu_recipes.inventory_history_id', '=', 'inventory_histories.id')
                    ->join('inventories', 'inventory_histories.inventory_id', '=', 'inventories.id')
                    ->whereColumn('menu_prices.id', 'menu_recipes.menu_price_id')
                    ->limit(1),
                'availability' => MenuRecipe::selectRaw('CASE WHEN FLOOR(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0))) > 0 THEN true ELSE false END')
                    ->join('inventory_histories', 'menu_recipes.inventory_history_id', '=', 'inventory_histories.id')
                    ->join('inventories', 'inventory_histories.inventory_id', '=', 'inventories.id')
                    ->whereColumn('menu_prices.id', 'menu_recipes.menu_price_id')
                    ->limit(1)
            ])
            ->get();

        $setNewInventoryTemp = $data->map(function ($item) use ($attributes) {
            $matchingData = collect($attributes['data'])->firstWhere('uuid', $item->uuid);
            if ($matchingData) {
                $qty = $matchingData['qty'];
                $item->recipes = $item->recipes->map(function ($recipe) use ($qty) {
                    $qtyAsked = $recipe->qty * $qty;
                    $recipe->history->inventory->qty = $recipe->history->inventory->qty - $qtyAsked;
                    return $recipe;
                });
            }
            return $item;
        });

        $setNewMenuStock = $setNewInventoryTemp->map(function ($item) {
            $stockRemaining = [];
            $item->recipes->map(function ($recipe) use (&$stockRemaining) {
                $result = floor($recipe->history->inventory->qty / $recipe->qty);
                array_push($stockRemaining, $result);
            });
            $item->stock_remaining = min($stockRemaining);
            if (min($stockRemaining) == 0) $item->availability = 0;
            return $item;
        });

        $filteredMenuStock = $setNewMenuStock;
        if ($param) {
            $filteredMenuStock = $filteredMenuStock->filter(function ($item) use ($param) {
                return stripos($item->menu->name, $param['q']) !== false;
            });
            if (array_key_exists('category_uuid', $param)) {
                $filteredMenuStock = $filteredMenuStock->filter(function ($item) use ($param) {
                    return $item->menu->category->uuid === $param['category_uuid'];
                });
            }
        }

        return $filteredMenuStock;
    }

    public function activatePrice(Menu $menu, MenuPrice $price)
    {
        parent::index()->whereHas('menu', fn ($q) => $q->where('id', $menu->id))->update(['status' => 'inactive']);
        $data = $price->update(['status' => 'active']);
        return $data;
    }
}

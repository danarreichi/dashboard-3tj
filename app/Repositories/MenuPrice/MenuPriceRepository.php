<?php

namespace App\Repositories\MenuPrice;

use App\Models\Inventory;
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
        $recipes = MenuRecipe::query()
            ->with('history.inventory')
            ->whereHas('price', function($q) use ($attributes) {
                $q->where('status', 'active');
                $q->whereIn('uuid', collect($attributes['data'])->pluck('uuid'));
            })
            ->get();

        $neededRecipes = $recipes->map(function($recipe) use ($attributes) {
            $matchingData = collect($attributes['data'])->firstWhere('uuid', $recipe->price->uuid);
            $recipe->qty_asked = $matchingData['qty'] * $recipe->qty;
            return $recipe;
        });

        $prices = collect($attributes['data'])->map(function($item) {
            $matchData = MenuPrice::where('uuid', $item['uuid'])->firstOrFail();
            $item['subtotal'] = $matchData->price * $item['qty'];
            return $item;
        });

        $inventories = Inventory::get();

        $neededRecipes->each(function($recipe) use (&$inventories) {
            $inventory = $inventories->where('id', $recipe->history->inventory->id)->first();
            if ($inventory) $inventory->qty -= $recipe->qty_asked;
        });

        // Prepare the subquery values
        $inventoriesSubQueryValues = $inventories->map(function ($inventory) {
            return "SELECT " . (int) $inventory->id . " AS id, " . (int) $inventory->qty . " AS qty";
        })->implode(' UNION ALL ');

        // Build the complete query with the subquery directly
        $inventoriesSubQuery = DB::table(DB::raw("($inventoriesSubQueryValues) AS inventories"));

        $data = parent::index()
            ->whereHas('menu', function($q) use ($param) {
                if($param){
                    $q->where('name', 'LIKE', '%'. $param['q']. '%');
                    if (array_key_exists('category_uuid', $param)) $q->whereHas('category', fn ($q) => $q->where('uuid', $param['category_uuid']));
                }
            })
            ->with(['menu'])
            ->where('status', 'active')
            ->addSelect([
                'stock_remaining' => MenuRecipe::selectRaw('FLOOR(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)))')
                    ->join('inventory_histories', 'menu_recipes.inventory_history_id', '=', 'inventory_histories.id')
                    ->joinSub($inventoriesSubQuery, 'inventories', function ($join) {
                        $join->on('inventory_histories.inventory_id', '=', 'inventories.id');
                    })
                    ->whereColumn('menu_prices.id', 'menu_recipes.menu_price_id')
                    ->limit(1),
                'availability' => MenuRecipe::selectRaw('CASE WHEN FLOOR(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0))) > 0 THEN true ELSE false END')
                    ->join('inventory_histories', 'menu_recipes.inventory_history_id', '=', 'inventory_histories.id')
                    ->joinSub($inventoriesSubQuery, 'inventories', function ($join) {
                        $join->on('inventory_histories.inventory_id', '=', 'inventories.id');
                    })
                    ->whereColumn('menu_prices.id', 'menu_recipes.menu_price_id')
                    ->limit(1)
            ])
            ->get();

        return [$data, $prices, $attributes['discount']];
    }

    public function activatePrice(Menu $menu, MenuPrice $price)
    {
        parent::index()->whereHas('menu', fn ($q) => $q->where('id', $menu->id))->update(['status' => 'inactive']);
        $data = $price->update(['status' => 'active']);
        return $data;
    }
}

<?php

namespace App\Repositories\Menu;

use App\Models\Menu;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;

class MenuRepository extends BaseRepository
{
    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $filters = [AllowedFilter::trashed()];
        $sorts = ['name', 'updated_at'];
        $query = parent::index($filters, $sorts);
        $query->when(request('uuids'), function($q){
            $q->whereHas('prices', fn($q) => $q->whereIn('uuid', request('uuids')));
            $q->with('price', function($q){
                $q->selectRaw('*, (
                    SELECT FLOOR(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)))
                    FROM menu_recipes
                    JOIN inventory_histories ON menu_recipes.inventory_history_id = inventory_histories.id
                    JOIN inventories ON inventory_histories.inventory_id = inventories.id
                    WHERE menu_prices.id = menu_recipes.menu_price_id
                ) as stock_remaining,
                 CASE
                    WHEN (
                        SELECT FLOOR(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)))
                        FROM menu_recipes
                        JOIN inventory_histories ON menu_recipes.inventory_history_id = inventory_histories.id
                        JOIN inventories ON inventory_histories.inventory_id = inventories.id
                        WHERE menu_prices.id = menu_recipes.menu_price_id
                    ) > 0 THEN true
                    ELSE false
                END as availability');
            });
        });
        if (request('q')) {
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%' . request('q') . '%');
                $q->orWhereHas('category', function($q) {
                    $q->where('name', 'LIKE', '%' . request('q') . '%');
                });
            });
        }
        return $query->paginate(request('limit', 15))->withQueryString();
    }
}

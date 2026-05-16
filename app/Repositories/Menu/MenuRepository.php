<?php

namespace App\Repositories\Menu;

use App\Models\Menu;
use App\Models\Sale;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
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
        $query = parent::index($filters, $sorts)->with('price');
        $query->when(request('uuids'), function ($q) {
            $q->whereHas('prices', fn ($q) => $q->whereIn('uuid', request('uuids')));
            $q->with('price', function ($q) {
                $q->selectRaw('*, (
                    SELECT CAST(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)) AS INTEGER)
                    FROM menu_recipes
                    JOIN inventory_histories ON menu_recipes.inventory_history_id = inventory_histories.id
                    JOIN inventories ON inventory_histories.inventory_id = inventories.id
                    WHERE menu_prices.id = menu_recipes.menu_price_id
                ) as stock_remaining,
                CASE
                    WHEN (
                        SELECT CAST(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)) AS INTEGER)
                        FROM menu_recipes
                        JOIN inventory_histories ON menu_recipes.inventory_history_id = inventory_histories.id
                        JOIN inventories ON inventory_histories.inventory_id = inventories.id
                        WHERE menu_prices.id = menu_recipes.menu_price_id
                    ) > 0 THEN 1
                    ELSE 0
                END as availability');
            });
        });
        if (request('q')) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', '%'.request('q').'%');
                $q->orWhereHas('category', function ($q) {
                    $q->where('name', 'LIKE', '%'.request('q').'%');
                });
            });
        }

        return $query->paginate(request('limit', 15))->withQueryString();
    }

    public function listMenuSales()
    {
        $filters = [AllowedFilter::trashed()];
        $sorts = ['name', 'updated_at'];
        $data = parent::index($filters, $sorts)
            ->with(['sales' => function ($q) {
                $q->with('price');
                if (request('start_between')) {
                    $startBetween = array_values(array_filter(explode(',', request('start_between'))));
                    if (count($startBetween) === 2) {
                        $start = Carbon::parse($startBetween[0])->startOfDay();
                        $end = Carbon::parse($startBetween[1])->endOfDay();
                        $q->where('sales.created_at', '>=', $start);
                        $q->where('sales.created_at', '<=', $end);
                    }
                }
            }]);

        if (request('q')) {
            $data->where(function ($q) {
                $q->where('name', 'LIKE', '%'.request('q').'%');
                $q->orWhereHas('category', function ($q) {
                    $q->where('name', 'LIKE', '%'.request('q').'%');
                });
            });
        }

        $minDate = Sale::min('created_at') ? Carbon::parse(Sale::min('created_at')) : null;
        $maxDate = Sale::max('created_at') ? Carbon::parse(Sale::max('created_at')) : null;

        return [$data->paginate(request('limit', 15))->withQueryString(), $minDate, $maxDate];
    }
}

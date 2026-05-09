<?php

namespace App\Repositories\Sale;

use App\Models\SaleGroup;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class SaleGroupRepository extends BaseRepository
{
    public function __construct(SaleGroup $model)
    {
        $this->model = $model;
    }

    public function earningsByDate()
    {
        $data = SaleGroup::query()
            ->withCalculation('sales', 'SUM(qty)', 'sales_qty')
            ->withCalculation('sales.price.inventoryHistories', 'SUM(inventory_histories.price / IFNULL(inventory_histories.qty, 1))', 'hpp')
            ->withCalculation('sales.price', 'SUM(price)', 'price')
            ->where(function ($q) {
                if (request('start_between')) {
                    $startBetween = array_values(array_filter(explode(",", request('start_between'))));
                    if (count($startBetween) === 2) {
                        $q->whereDate('sale_groups.created_at', '>=', $startBetween[0]);
                        $q->whereDate('sale_groups.created_at', '<=', $startBetween[1]);
                    }
                }
            });
        $data = DB::query()
            ->selectRaw('
                SUM(sales_qty * price) as total_sum, 
                SUM((sales_qty * price) - (hpp * sales_qty)) as total_sum_clean, 
                SUM(discount) as discount_sum, 
                SUM((sales_qty * price) - discount) as total_after_discount_sum, 
                SUM(((sales_qty * price) - (hpp * sales_qty)) - discount) as total_after_discount_sum_clean, 
                SUM(hpp * sales_qty) as hpp
            ')
            ->fromSub($data->toBase(), 't')
            ->first();

        return $data;
    }
}

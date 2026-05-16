<?php

namespace App\Repositories\Sale;

use App\Models\SaleGroup;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleGroupRepository extends BaseRepository
{
    public function __construct(SaleGroup $model)
    {
        $this->model = $model;
    }

    public function earningsByDate()
    {
        $salesFilter = function ($q) {
            if (request('start_between')) {
                $startBetween = array_values(array_filter(explode(',', request('start_between'))));
                if (count($startBetween) === 2) {
                    $start = Carbon::parse($startBetween[0])->startOfDay();
                    $end = Carbon::parse($startBetween[1])->endOfDay();
                    $q->where(function ($q) use ($start, $end) {
                        $q->whereDate('sales.created_at', '>=', $start);
                        $q->whereDate('sales.created_at', '<=', $end);
                    });
                }
            }
        };
        $data = SaleGroup::query()
            ->withCalculation('sales.price', 'SUM(sales.qty * menu_prices.price)', 'total', $salesFilter)
            ->withCalculation('sales.price.inventoryHistories', 'SUM(sales.qty * inventory_histories.price / IFNULL(inventory_histories.qty, 1))', 'hpp', $salesFilter)
            ->where(function ($q) {
                if (request('start_between')) {
                    $startBetween = array_values(array_filter(explode(',', request('start_between'))));
                    if (count($startBetween) === 2) {
                        $start = Carbon::parse($startBetween[0])->startOfDay();
                        $end = Carbon::parse($startBetween[1])->endOfDay();
                        $q->whereDate('sale_groups.created_at', '>=', $start);
                        $q->whereDate('sale_groups.created_at', '<=', $end);
                    }
                }
            });
        $data = DB::query()
            ->selectRaw('
                SUM(total) as total_sum,
                SUM(total - hpp) as total_sum_clean,
                SUM(discount) as discount_sum,
                SUM(total - discount) as total_after_discount_sum,
                SUM((total - hpp) - discount) as total_after_discount_sum_clean,
                SUM(hpp) as hpp
            ')
            ->fromSub($data->toBase(), 't')
            ->first();

        return $data;
    }
}

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
            ->selectRaw('SUM(total) as total_sum, SUM(total_after_discount) as total_after_discount_sum, SUM(discount) as discount_sum')
            ->where(function ($q) {
                if (request('start_between')) {
                    $startBetween = array_values(array_filter(explode(",", request('start_between'))));
                    if (count($startBetween) === 2) {
                        $q->whereDate('sale_groups.created_at', '>=', $startBetween[0]);
                        $q->whereDate('sale_groups.created_at', '<=', $startBetween[1]);
                    }
                }
            })->first();

        return $data;
    }
}

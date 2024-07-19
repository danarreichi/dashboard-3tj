<?php

namespace App\Repositories\Sale;

use App\Models\MenuPrice;
use App\Models\Sale;
use App\Models\SaleGroup;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SaleRepository extends BaseRepository
{
    public function __construct(Sale $model)
    {
        $this->model = $model;
    }

    public function createCheckout(array $attributes)
    {
        $data = MenuPrice::query()
            ->whereHas('menu')
            ->where('status', 'active')
            ->whereIn('uuid', collect($attributes['data'])->pluck('uuid'))
            ->get();

        $data = $data->map(function ($item) use ($attributes) {
            $relatedData = collect($attributes['data'])->firstWhere('uuid', $item->uuid);
            $item->asked_qty = $relatedData['qty'];
            $item->subtotal = $item->price * $relatedData['qty'];
            return $item;
        });

        $total = $data->sum('subtotal');
        $convertedDiscount = ($attributes['discount']['type'] === "persentase") ? ($data->sum('subtotal') * ($attributes['discount']['qty'] / 100)) : $attributes['discount']['qty'];
        $totalAfterDiscount = $total - $convertedDiscount;

        $saleGroup = SaleGroup::create([
            'total' => $total,
            'discount' => $convertedDiscount,
            'total_after_discount' => ($totalAfterDiscount < 0) ? 0 : $totalAfterDiscount,
            'item_qty' => $data->count(),
            'note' => $attributes['payment_method'],
        ]);

        $data->each(function ($item) use ($saleGroup) {
            parent::create([
                'sale_group_id' => $saleGroup->id,
                'menu_price_id' => $item->id,
                'qty' => $item->asked_qty
            ]);
        });

        return $saleGroup;
    }
}

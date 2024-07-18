<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\SaleGroup;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryRepository extends BaseRepository
{
    public function __construct(Inventory $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $filters = [AllowedFilter::trashed()];
        $sorts = ['name', 'qty', 'unit', 'updated_at'];
        $query = parent::index($filters, $sorts);
        if (request('q')) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request('q') . '%');
                $q->orWhere('unit', 'LIKE', '%' . request('q') . '%');
            });
        }
        return $query->paginate(request('limit', 15))->withQueryString();
    }

    public function listDropdown()
    {
        $data = parent::index()->orderBy('name');
        if (request('excludes')) {
            $data->whereNotIn('uuid', explode(',', request('excludes')));
        }
        return $data->get();
    }

    public function adjustQty(Inventory $inventory, array $attributes)
    {
        $query = self::update($inventory, [
            'qty' => ($attributes['status'] === 'out') ? $inventory->qty - $attributes['qty'] : $inventory->qty + $attributes['qty']
        ]);
        return $query;
    }

    public function reduceInventoryStock(SaleGroup $saleGroup)
    {
        $saleGroup->sales->each(function($sale){
            $qty = $sale->qty;
            $sale->price->recipes->each(function($recipe) use ($qty) {
                $qtyNeeded = $recipe->qty * $qty;
                $inventoryQtyBefore = $recipe->history->inventory->qty;
                $inventoryQtyAfter = $inventoryQtyBefore - $qtyNeeded;
                $recipe->history->inventory->update([
                    'qty' => $inventoryQtyAfter
                ]);
                $price = ($recipe->history->price / $recipe->history->qty) * $qtyNeeded;
                InventoryHistory::create([
                    'inventory_id' => $recipe->history->inventory->id,
                    'user_id' => Auth::user()->id,
                    'status' => InventoryHistory::STATUS_OUT,
                    'qty' => $qtyNeeded,
                    'price' => $price,
                    'payload' => [
                        'old_qty' => $inventoryQtyBefore,
                        'new_qty' => $inventoryQtyAfter
                    ]
                ]);
            });
        });
        return $saleGroup;
    }
}

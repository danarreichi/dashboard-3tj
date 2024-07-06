<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory;
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
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%' . request('q') . '%');
                $q->orWhere('unit', 'LIKE', '%' . request('q') . '%');
            });
        }
        return $query->paginate(request('limit', 15))->withQueryString();
    }

    public function adjustQty(Inventory $inventory, array $attributes)
    {
        $query = self::update($inventory, [
            'qty' => ($attributes['status'] === 'out')? $inventory->qty - $attributes['qty'] : $inventory->qty + $attributes['qty']
        ]);
        return $query;
    }
}

<?php

namespace App\Repositories\MenuPrice;

use App\Models\Menu;
use App\Models\MenuPrice;
use App\Repositories\BaseRepository;
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
            $query->where(function($q) {
                $q->where('price', 'LIKE', '%' . request('q') . '%');
                $q->orWhere('status', 'LIKE', '%' . request('q') . '%');
            });
        }
        return $query->paginate(request('limit', 15))->withQueryString();
    }

    public function activatePrice(Menu $menu, MenuPrice $price)
    {
        parent::index()->whereHas('menu', fn($q) => $q->where('id', $menu->id))->update(['status' => 'inactive']);
        $data = $price->update(['status' => 'active']);
        return $data;
    }
}

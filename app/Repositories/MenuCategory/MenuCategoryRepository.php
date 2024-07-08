<?php

namespace App\Repositories\MenuCategory;

use App\Models\Inventory;
use App\Models\MenuCategory;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;

class MenuCategoryRepository extends BaseRepository
{
    public function __construct(MenuCategory $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $filters = [AllowedFilter::trashed()];
        $sorts = ['name', 'updated_at'];
        $query = parent::index($filters, $sorts)->withCount('menus');
        if (request('q')) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request('q') . '%');
            });
        }
        return $query->paginate(request('limit', 15))->withQueryString();
    }

    public function listDropdown()
    {
        $query = parent::index([], []);
        if (request('q')) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request('q') . '%');
            });
        }
        return $query->get();
    }
}

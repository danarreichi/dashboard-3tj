<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryHistoryRepository extends BaseRepository
{
    public function __construct(InventoryHistory $model)
    {
        $this->model = $model;
    }

    public function listByAccount(User $user)
    {
        abort_if((Auth::user()->userRole->id === 'user' && (Auth::user()->id !== $user->id)), 403, __("Can't see other user history"));
        $filters = [AllowedFilter::scope('start_between')];
        $sorts = ['updated_at'];
        $query = parent::index($filters, $sorts)->with('inventory')->where('user_id', $user->id)->orderByDesc('id');
        if (request('q')) $query->whereHas('inventory', fn ($q) => $q->where('name', 'LIKE', '%' . request('q') . '%'));

        $dateMin = InventoryHistory::where('user_id', $user->id)->min('created_at');
        $dateMax = InventoryHistory::where('user_id', $user->id)->max('created_at');

        return [$query->paginate(request('limit', 15))->withQueryString(), $dateMin, $dateMax];
    }

    public function listDropdownByInventory(Inventory $inventory)
    {
        $query = parent::index()->where('inventory_id', $inventory->id)->where('status', InventoryHistory::STATUS_IN)->orderByDesc('id');
        return $query->limit(5)->get();
    }

    public function listByInventory(Inventory $inventory)
    {
        $filters = [AllowedFilter::scope('start_between')];
        $sorts = ['updated_at'];
        $query = parent::index($filters, $sorts)->with('user', 'inventory')->where('inventory_id', $inventory->id)->orderByDesc('id');
        if (request('q')) $query->whereHas('user', fn ($q) => $q->where('name', 'LIKE', '%' . request('q') . '%'));

        $dateMin = InventoryHistory::where('inventory_id', $inventory->id)->min('created_at');
        $dateMax = InventoryHistory::where('inventory_id', $inventory->id)->max('created_at');

        return [$query->paginate(request('limit', 15))->withQueryString(), $dateMin, $dateMax];
    }
}

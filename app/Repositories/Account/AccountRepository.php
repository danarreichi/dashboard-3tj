<?php

namespace App\Repositories\Account;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;

class AccountRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $filters = [AllowedFilter::trashed()];
        $sorts = ['updated_at'];
        $query = parent::index($filters, $sorts);
        if(Auth::user()->userRole->id === 'user') $query->where('id', Auth::user()->id);
        if (request('q')) {
            $query->where(function($q) {
                $q->where('name', 'LIKE', '%' . request('q') . '%');
                $q->orWhereHas('userRole', function($q) {
                    $q->where('name', 'LIKE', '%' . request('q') . '%');
                });
            });
        }
        return $query->paginate(request('limit', 15))->withQueryString();
    }
}

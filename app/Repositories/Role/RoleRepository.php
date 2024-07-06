<?php

namespace App\Repositories\Role;

use App\Models\UserRole;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleRepository extends BaseRepository
{
    public function __construct(UserRole $model)
    {
        $this->model = $model;
    }

    public function getRoleDropdown()
    {
        $query = parent::index();
        return $query->get();
    }
}

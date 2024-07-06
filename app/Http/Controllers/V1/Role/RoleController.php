<?php

namespace App\Http\Controllers\V1\Role;

use App\Http\Controllers\Controller;
use App\Http\Resources\Console\V1\RoleDropdownResource;
use App\Repositories\Role\RoleRepository;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRoleDropdown()
    {
        return RoleDropdownResource::collection($this->repository->getRoleDropdown());
    }
}

<?php

namespace App\Http\Controllers\V1\MenuCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Console\V1\StoreMenuCategoryRequest;
use App\Http\Requests\Console\V1\UpdateMenuCategoryRequest;
use App\Http\Resources\Console\V1\InventoryResource;
use App\Http\Resources\Console\V1\MenuCategoryResource;
use App\Models\MenuCategory;
use App\Repositories\MediafileRepository;
use App\Repositories\MenuCategory\MenuCategoryRepository;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class MenuCategoryController extends Controller
{
    private $repository;

    public function __construct(MenuCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $data = $this->repository->list();
        return MenuCategoryResource::collection($data)->additional([
            'meta' => [
                'logined_role' => Auth::user()->userRole->id
            ]
        ]);
    }

    public function dropdown()
    {
        return MenuCategoryResource::collection($this->repository->listDropdown());
    }

    public function store(StoreMenuCategoryRequest $request)
    {
        $menuCategory = DB::transaction(function () use ($request) {
            $data = $this->repository->create($request->validated());
            return $data;
        });
        return new MenuCategoryResource($menuCategory);
    }

    public function show(MenuCategory $menuCategory)
    {
        return new MenuCategoryResource($menuCategory);
    }

    public function update(UpdateMenuCategoryRequest $request, MenuCategory $menuCategory)
    {
        $menuCategory = DB::transaction(function () use ($request, $menuCategory) {
            $data = $this->repository->update($menuCategory, $request->validated());
            return $data;
        });
        return new MenuCategoryResource($menuCategory);
    }

    public function destroy(MenuCategory $menuCategory)
    {
        abort_if(Auth::user()->userRole->id === 'user', 403, __("User tidak bisa menghapus kategori menu"));
        return $this->repository->destroy($menuCategory);
    }

    public function restore($id)
    {
        $menuCategory = $this->repository->restore($id);
        return new MenuCategoryResource($menuCategory);
    }
}

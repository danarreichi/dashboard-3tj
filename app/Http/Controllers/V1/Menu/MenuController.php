<?php

namespace App\Http\Controllers\V1\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Console\V1\StoreMenuRequest;
use App\Http\Requests\Console\V1\UpdateMenuRequest;
use App\Http\Resources\Console\V1\MenuResource;
use App\Models\Menu;
use App\Repositories\MediafileRepository;
use App\Repositories\Menu\MenuRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    private $repository, $mediafileRepository;

    public function __construct(MenuRepository $repository, MediafileRepository $mediafileRepository)
    {
        $this->repository = $repository;
        $this->mediafileRepository = $mediafileRepository;
    }

    public function index()
    {
        $data = $this->repository->list();
        return MenuResource::collection($data)->additional([
            'meta' => [
                'logined_role' => Auth::user()->userRole->id
            ]
        ]);
    }

    public function store(StoreMenuRequest $request)
    {
        $attributes = [
            'name' => $request->name,
            'menu_category_id' => $request->menu_category_id
        ];
        $menu = DB::transaction(function () use ($request, $attributes) {
            $data = $this->repository->create($attributes);
            $this->mediafileRepository->createByModel($data, "menu/{$data->uuid}", $request->image);
            return $data;
        });
        return new MenuResource($menu);
    }

    public function show(Menu $menu)
    {
        return new MenuResource($menu);
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $attributes = [
            'name' => $request->name,
            'menu_category_id' => $request->menu_category_id
        ];
        $menu = DB::transaction(function () use ($attributes, $menu, $request) {
            $data = $this->repository->update($menu, $attributes);
            if($request->has('image')) $this->mediafileRepository->replaceMediaWithDelete($menu->image, "menu/{$data->uuid}", $request->image);
            return $data;
        });
        return new MenuResource($menu);
    }

    public function destroy(Menu $menu)
    {
        abort_if(Auth::user()->userRole->id === 'user', 403, __("User tidak bisa menghapus menu"));
        return $this->repository->destroy($menu);
    }

    public function restore($id)
    {
        $menu = $this->repository->restore($id);
        return new MenuResource($menu);
    }
}

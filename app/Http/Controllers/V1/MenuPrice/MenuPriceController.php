<?php

namespace App\Http\Controllers\V1\MenuPrice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Console\V1\RefreshMenuStockRequest;
use App\Http\Requests\Console\V1\StoreMenuPriceRequest;
use App\Http\Requests\Console\V1\UpdateMenuRequest;
use App\Http\Resources\Console\V1\ActiveMenuPriceResource;
use App\Http\Resources\Console\V1\MenuPriceResource;
use App\Http\Resources\Console\V1\MenuResource;
use App\Models\Menu;
use App\Models\MenuPrice;
use App\Repositories\MenuPrice\MenuPriceRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class MenuPriceController extends Controller
{
    private $repository;

    public function __construct(MenuPriceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Menu $menu)
    {
        $data = $this->repository->listByMenu($menu);
        return MenuPriceResource::collection($data)->additional([
            'meta' => [
                'logined_role' => Auth::user()->userRole->id,
                'menu_name' => $menu->name,
                'menu_uuid' => $menu->uuid,
            ]
        ]);
    }

    public function store(Menu $menu, StoreMenuPriceRequest $request)
    {
        $data = DB::transaction(function () use ($menu, $request) {
            $menuPrice = $this->repository->insertPriceAndRecipes($menu, $request->validated());
            $menuPrice->load('recipes');
            return $menuPrice;
        });
        return $data;
    }

    public function listActivePrice()
    {
        $data = $this->repository->listActivePrice();
        return ActiveMenuPriceResource::collection($data);
    }

    public function listActivePriceTemp(RefreshMenuStockRequest $request)
    {
        [$data, $prices, $discount] = $this->repository->listActivePriceTemp($request->validated(), $request['query_params']);
        $subtotal = collect($prices)->sum('subtotal');
        $convertedDiscount = ($discount['type'] === "persentase") ? ($subtotal * ($discount['qty'] / 100)) : $discount['qty'];
        $total = ($subtotal - $convertedDiscount);
        return ActiveMenuPriceResource::collection($data)->additional([
            'meta' => [
                'subtotal' => "Rp" . number_format($subtotal, 2, ",", "."),
                'discount' => "Rp" . number_format($convertedDiscount, 2, ",", "."),
                'total' => "Rp" . number_format(($total < 0) ? 0 : $total, 2, ",", "."),
                'total_calc' => ($total < 0) ? 0 : $total,
            ]
        ]);
    }

    public function show(Menu $menu, MenuPrice $menuPrice)
    {
    }

    public function update(UpdateMenuRequest $request, Menu $menu, MenuPrice $menuPrice)
    {
    }

    public function destroy(Menu $menu, MenuPrice $menuPrice)
    {
    }

    public function activate(Menu $menu, MenuPrice $price)
    {
        $price = DB::transaction(function () use ($menu, $price) {
            return $this->repository->activatePrice($menu, $price);
        });
        return $price;
    }

    public function restore($id)
    {
        $menu = $this->repository->restore($id);
        return new MenuResource($menu);
    }
}

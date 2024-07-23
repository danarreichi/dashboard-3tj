<?php

namespace App\Http\Controllers\V1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Console\V1\CheckoutRequest;
use App\Http\Resources\Console\V1\MenuSaleResource;
use App\Http\Resources\Console\V1\SaleResource;
use App\Models\Menu;
use App\Repositories\Inventory\InventoryRepository;
use App\Repositories\Menu\MenuRepository;
use App\Repositories\Sale\SaleRepository;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private $repository, $menuRepository, $inventoryRepository;

    public function __construct(SaleRepository $repository, InventoryRepository $inventoryRepository, MenuRepository $menuRepository)
    {
        $this->repository = $repository;
        $this->menuRepository = $menuRepository;
        $this->inventoryRepository = $inventoryRepository;
    }

    public function index()
    {
        [$menuSales, $minDate, $maxDate] = $this->menuRepository->listMenuSales();
        return MenuSaleResource::collection($menuSales)->additional([
            'meta' => [
                'start_date' => $minDate,
                'end_date' => $maxDate
            ]
        ]);
    }

    public function menuSale($menu)
    {
        $menu = Menu::withTrashed()->where('uuid', $menu)->firstOrFail();
        [$data, $countSale, $totalSale] = $this->repository->listSalesByMenu($menu);

        return SaleResource::collection($data)->additional([
            'meta' => [
                'product_name' => $menu->name,
                'count_sale' => $countSale,
                'total_sale' => "Rp" . number_format($totalSale, 2, ",", ".")
            ]
        ]);
    }

    public function checkout(CheckoutRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $sale = $this->repository->createCheckout($request->validated());
            $sale = $this->inventoryRepository->reduceInventoryStock($sale);
            return $sale;
        });
    }
}

<?php

namespace App\Http\Controllers\V1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Console\V1\CheckoutRequest;
use App\Http\Resources\Console\V1\MenuSaleResource;
use App\Http\Resources\Console\V1\SaleResource;
use App\Models\Menu;
use App\Repositories\Inventory\InventoryRepository;
use App\Repositories\Sale\SaleRepository;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private $repository, $inventoryRepository;

    public function __construct(SaleRepository $repository, InventoryRepository $inventoryRepository)
    {
        $this->repository = $repository;
        $this->inventoryRepository = $inventoryRepository;
    }

    public function index()
    {
        [$menuSales, $minDate, $maxDate] = $this->repository->listMenuSales();
        return MenuSaleResource::collection($menuSales)->additional([
            'meta' => [
                'start_date' => $minDate,
                'end_date' => $maxDate
            ]
        ]);
    }

    public function menuSale(Menu $menu)
    {
        [$data, $minDate, $maxDate] = $this->repository->listSalesByMenu($menu);
        return SaleResource::collection($data)->additional([
            'meta' => [
                'min_date' => $minDate,
                'max_date' => $maxDate
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

<?php

namespace App\Http\Controllers\V1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Console\V1\CheckoutRequest;
use App\Repositories\Inventory\InventoryRepository;
use App\Repositories\Sale\SaleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private $repository, $inventoryRepository;

    public function __construct(SaleRepository $repository, InventoryRepository $inventoryRepository)
    {
        $this->repository = $repository;
        $this->inventoryRepository = $inventoryRepository;
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

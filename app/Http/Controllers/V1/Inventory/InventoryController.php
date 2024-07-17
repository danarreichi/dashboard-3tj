<?php

namespace App\Http\Controllers\V1\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Console\V1\AdjustInventoryRequest;
use App\Http\Requests\Console\V1\StoreInventoryRequest;
use App\Http\Requests\Console\V1\UpdateAccountRequest;
use App\Http\Requests\Console\V1\UpdateInventoryRequest;
use App\Http\Resources\Console\V1\InventoryHistoriesResource;
use App\Http\Resources\Console\V1\InventoryResource;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Repositories\Inventory\InventoryHistoryRepository;
use App\Repositories\Inventory\InventoryRepository;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    private $repository, $inventoryHistoryRepository;

    public function __construct(InventoryRepository $repository, InventoryHistoryRepository $inventoryHistoryRepository)
    {
        $this->repository = $repository;
        $this->inventoryHistoryRepository = $inventoryHistoryRepository;
    }

    public function index()
    {
        $data = $this->repository->list();
        return InventoryResource::collection($data)->additional([
            'meta' => [
                'logined_role' => Auth::user()->userRole->id
            ]
        ]);
    }

    public function store(StoreInventoryRequest $request)
    {
        $inventory = DB::transaction(function () use ($request) {
            $data = $this->repository->create(Arr::except($request->validated(), 'price'));
            $this->inventoryHistoryRepository->create([
                'inventory_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => InventoryHistory::STATUS_IN,
                'qty' => $data->qty,
                'price' => $request->validated()['price'],
                'payload' => [
                    'old_qty' => 0,
                    'new_qty' => $data->qty
                ]
            ]);
            return $data;
        });
        return new InventoryResource($inventory);
    }

    public function show(Inventory $inventory)
    {
        $inventory->load(['histories' => fn ($q) => $q->orderByDesc('created_at')->limit(5)]);
        return new InventoryResource($inventory);
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $inventory = DB::transaction(function () use ($request, $inventory) {
            $data = $this->repository->update($inventory, $request->validated());
            return $data;
        });
        return new InventoryResource($inventory);
    }

    public function destroy(Inventory $inventory)
    {
        abort_if(Auth::user()->userRole->id === 'user', 403, __("User tidak bisa menghapus inventory"));
        return $this->repository->destroy($inventory);
    }

    public function restore($id)
    {
        $inventory = $this->repository->restore($id);
        return new InventoryResource($inventory);
    }

    public function adjust(AdjustInventoryRequest $request, Inventory $inventory)
    {
        $inventory = DB::transaction(function () use ($request, $inventory) {
            $attributes = $request->validated();
            $oldQty = $inventory->qty;
            $updatedInventory = $this->repository->adjustQty($inventory, $attributes);
            $this->inventoryHistoryRepository->create([
                'inventory_id' => $updatedInventory->id,
                'user_id' => Auth::user()->id,
                'status' => $attributes['status'],
                'qty' => $attributes['qty'],
                'price' => $attributes['price'],
                'payload' => [
                    'old_qty' => $oldQty,
                    'new_qty' => $updatedInventory->qty
                ]
            ]);
            return $updatedInventory;
        });
        $inventory->load(['histories' => fn ($q) => $q->orderByDesc('created_at')->limit(5)]);
        return new InventoryResource($inventory);
    }

    public function history(Inventory $inventory)
    {
        [$history, $dateMin, $dateMax] = $this->inventoryHistoryRepository->listByInventory($inventory);
        return InventoryHistoriesResource::collection($history)->additional([
            'meta' => [
                'date_min' => $dateMin,
                'date_max' => $dateMax
            ]
        ]);
    }

    public function dropdownHistory(Inventory $inventory)
    {
        $data = $this->inventoryHistoryRepository->listDropdownByInventory($inventory);
        return InventoryHistoriesResource::collection($data);
    }

    public function dropdown()
    {
        return InventoryResource::collection($this->repository->listDropdown());
    }
}

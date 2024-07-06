<?php

namespace App\Http\Controllers\V1\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Console\V1\StoreAccountRequest;
use App\Http\Requests\Console\V1\UpdateAccountRequest;
use App\Http\Resources\Console\V1\AccountHistoriesResource;
use App\Http\Resources\Console\V1\AccountResource;
use App\Models\User;
use App\Repositories\Account\AccountRepository;
use App\Repositories\Inventory\InventoryHistoryRepository;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    private $repository, $inventoryHistoryRepository;

    public function __construct(AccountRepository $repository, InventoryHistoryRepository $inventoryHistoryRepository)
    {
        $this->repository = $repository;
        $this->inventoryHistoryRepository = $inventoryHistoryRepository;
    }

    public function index()
    {
        $data = $this->repository->list();
        return AccountResource::collection($data)->additional([
            'meta' => [
                'logined_role' => Auth::user()->userRole->id
            ]
        ]);
    }

    public function store(StoreAccountRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            return $this->repository->create($request->validated());
        });
        return new AccountResource($user);
    }

    public function show(User $account)
    {
        return new AccountResource($account);
    }

    public function update(UpdateAccountRequest $request, User $account)
    {
        $data = (!$request->validated()['password']) ? Arr::except($request->validated(), 'password') : $request->validated();
        $account = $this->repository->update($account, $data);
        $account->load('userRole');
        return new AccountResource($account);
    }

    public function destroy(User $account)
    {
        abort_if(Auth::user()->id === $account->id, 403, __("Anda tidak dapat memblokir akun Anda"));
        return $this->repository->destroy($account);
    }

    public function restore($id)
    {
        $account = $this->repository->restore($id);
        return new AccountResource($account);
    }

    public function history(User $user)
    {
        [$history, $dateMin, $dateMax] = $this->inventoryHistoryRepository->listByAccount($user);
        return AccountHistoriesResource::collection($history)->additional([
            'meta' => [
                'date_min' => $dateMin,
                'date_max' => $dateMax
            ]
        ]);
    }
}

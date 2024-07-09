<?php

use App\Http\Controllers\V1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [V1\LoginController::class, 'authorization'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('profile', [V1\LoginController::class, 'getProfile'])->name('profile');

    Route::apiResource('account', V1\Account\AccountController::class)->names('account');
    Route::get('account/{id}/restore', [V1\Account\AccountController::class, 'restore'])->name('account-restore');
    Route::get('account/{user}/history', [V1\Account\AccountController::class, 'history'])->name('account-history');

    Route::apiResource('inventory', V1\Inventory\InventoryController::class)->names('inventory');
    Route::get('inventory/{id}/restore', [V1\Inventory\InventoryController::class, 'restore'])->name('inventory-restore');
    Route::get('inventory/{inventory}/history', [V1\Inventory\InventoryController::class, 'history'])->name('inventory-history');
    Route::post('inventory/{inventory}/adjust', [V1\Inventory\InventoryController::class, 'adjust'])->name('inventory-adjust');

    Route::apiResource('menu-category', V1\MenuCategory\MenuCategoryController::class)->names('menu-category');
    Route::get('menu-category/{menuCategory}/restore', [V1\MenuCategory\MenuCategoryController::class, 'restore'])->name('menu-category-restore');

    Route::apiResource('menu', V1\Menu\MenuController::class)->names('menu');
    Route::apiResource('menu/{menu}/price', V1\MenuPrice\MenuPriceController::class)->names('menu-price');
    Route::get('menu/{menu}/price/{price}/activate', [V1\MenuPrice\MenuPriceController::class, 'activate'])->name('menu-price-activate');
    Route::get('menu/{menu}/restore', [V1\Menu\MenuController::class, 'restore'])->name('menu-restore');

    Route::get('dropdown/menu-category', [V1\MenuCategory\MenuCategoryController::class, 'dropdown'])->name('dropdown-menu-category');
    Route::get('dropdown/inventory', [V1\Inventory\InventoryController::class, 'dropdown'])->name('dropdown-inventory');
    Route::get('dropdown/inventory/{inventory}/history', [V1\Inventory\InventoryController::class, 'dropdownHistory'])->name('dropdown-inventory-history');
    Route::get('dropdown/role', [V1\Role\RoleController::class, 'dropdown'])->name('dropdown-role');
});

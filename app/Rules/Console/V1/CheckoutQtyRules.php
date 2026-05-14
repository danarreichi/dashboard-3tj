<?php

namespace App\Rules\Console\V1;

use App\Models\Inventory;
use App\Models\MenuPrice;
use App\Models\MenuRecipe;
use Illuminate\Contracts\Validation\Rule;

class CheckoutQtyRules implements Rule
{
    private $attributes;

    private $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $index = explode('.', $attribute)[1];
        $allValue = $this->attributes[$index];
        $data = MenuPrice::query()
            ->whereHas('menu')
            ->with(['menu', 'recipes.history.inventory'])
            ->where('status', 'active')
            ->whereIn('uuid', collect($this->attributes)->pluck('uuid'))
            ->addSelect([
                'stock_remaining' => MenuRecipe::selectRaw('CAST(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)) AS INTEGER)')
                    ->join('inventory_histories', 'menu_recipes.inventory_history_id', '=', 'inventory_histories.id')
                    ->join('inventories', 'inventory_histories.inventory_id', '=', 'inventories.id')
                    ->whereNot('inventories.stock_type', Inventory::FIXED)
                    ->whereColumn('menu_prices.id', 'menu_recipes.menu_price_id')
                    ->limit(1),
                'availability' => MenuRecipe::selectRaw('CASE WHEN MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)) IS NULL OR CAST(MIN(COALESCE(inventories.qty / menu_recipes.qty, 0)) AS INTEGER) > 0 THEN 1 ELSE 0 END')
                    ->join('inventory_histories', 'menu_recipes.inventory_history_id', '=', 'inventory_histories.id')
                    ->join('inventories', 'inventory_histories.inventory_id', '=', 'inventories.id')
                    ->whereNot('inventories.stock_type', Inventory::FIXED)
                    ->whereColumn('menu_prices.id', 'menu_recipes.menu_price_id')
                    ->limit(1),
            ])
            ->get();

        $setNewInventoryTemp = $data->map(function ($item) {
            $matchingData = collect($this->attributes)->firstWhere('uuid', $item->uuid);
            if ($matchingData) {
                $qty = $matchingData['qty'];
                $item->subtotal = $qty * $item->price;
                $item->recipes = $item->recipes->map(function ($recipe) use ($qty) {
                    if ($recipe->history->inventory->stock_type !== Inventory::FIXED) {
                        $qtyAsked = $recipe->qty * $qty;
                        $recipe->history->inventory->qty = $recipe->history->inventory->qty - $qtyAsked;
                    }

                    return $recipe;
                });
            }

            return $item;
        });

        $setNewMenuStock = $setNewInventoryTemp->map(function ($item) {
            $stockRemaining = [];
            $item->recipes->map(function ($recipe) use (&$stockRemaining) {
                if ($recipe->history->inventory->stock_type === Inventory::FIXED) return;
                $result = floor($recipe->history->inventory->qty / $recipe->qty);
                array_push($stockRemaining, $result);
            });
            $item->stock_remaining = empty($stockRemaining) ? null : min($stockRemaining);
            if (!empty($stockRemaining) && min($stockRemaining) == 0) {
                $item->availability = 0;
            }

            return $item;
        });

        $menuItem = $setNewMenuStock->firstWhere('uuid', $allValue['uuid']);
        if ($menuItem->stock_remaining !== null && $menuItem->stock_remaining < 0) {
            $this->errorMessage = 'Stok menu tidak mencukupi';

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}

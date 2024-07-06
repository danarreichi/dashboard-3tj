<?php

namespace App\Rules\Console\V1;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use Illuminate\Contracts\Validation\Rule;

class AdjustInventoryQtyRules implements Rule
{
    private $inventory, $errorMessage, $all;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Inventory $inventory, $all)
    {
        $this->inventory = $inventory;
        $this->all = $all;
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
        if (($this->inventory->qty) < $value && ($this->all->status === InventoryHistory::STATUS_OUT)) {
            $this->errorMessage = __('Qty anda melebihi stok yang tersedia, stok tersedia: ' . $this->inventory->qty);
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

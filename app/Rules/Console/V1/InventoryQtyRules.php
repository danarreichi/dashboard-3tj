<?php

namespace App\Rules\Console\V1;

use Illuminate\Contracts\Validation\Rule;

class InventoryQtyRules implements Rule
{
    private $stockTypeAttribute, $errorMessage;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $stockTypeAttribute)
    {
        $this->stockTypeAttribute = $stockTypeAttribute;
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
       if ($this->stockTypeAttribute == 'reducible') {
            if (!varlen($value)) {
                $this->errorMessage = "qty minimal bernilai 1.";
                return false;
            }

            if ($value < 1) {
                $this->errorMessage = "qty minimal bernilai 1.";
                return false;
            }
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

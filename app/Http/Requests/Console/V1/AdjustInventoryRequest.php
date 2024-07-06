<?php

namespace App\Http\Requests\Console\V1;

use App\Rules\Console\V1\AdjustInventoryQtyRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdjustInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'status' => ['required', 'in:in,out'],
            'qty' => ['required', 'numeric', 'min:1', new AdjustInventoryQtyRules($this->inventory, $this)],
        ];
    }
}

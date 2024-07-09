<?php

namespace App\Http\Requests\Console\V1;

use App\Models\MenuCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreMenuPriceRequest extends FormRequest
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
            'price' => ['numeric', 'required', 'min:1'],
            'recipes' => ['required', 'array'],
            'recipes.*.uuid' => ['required', 'distinct', Rule::exists('inventory_histories', 'uuid')]
        ];
    }
}

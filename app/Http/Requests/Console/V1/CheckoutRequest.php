<?php

namespace App\Http\Requests\Console\V1;

use App\Rules\Console\V1\CheckoutQtyRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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
            'data' => ['required', 'array', 'min:1'],
            'data.*.uuid' => ['required', Rule::exists('menu_prices', 'uuid')->where('status', 'active')],
            'data.*.qty' => ['required', 'numeric', 'min:1', new CheckoutQtyRules($this->data)],
            'discount' => ['nullable', Rule::exists('discounts', 'code')],
            'payment_method' => ['required', 'in:cash,qris'],
        ];
    }
}

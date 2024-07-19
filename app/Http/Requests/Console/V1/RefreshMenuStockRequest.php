<?php

namespace App\Http\Requests\Console\V1;

use App\Models\MenuCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RefreshMenuStockRequest extends FormRequest
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
            'data.*.qty' => ['required', 'numeric', 'min:0'],
            'discount.qty' => ['required', 'numeric', 'min:0', Rule::when($this->input('discount.type') === 'persentase', ['max:100'])],
            'discount.type' => ['required', 'in:persentase,nominal'],
            'query_params' => ['required', 'array'],
            'query_params.*.category_uuid' => ['nullable', Rule::exists('menu_categories', 'uuid')],
            'query_params.*.q' => ['nullable'],
        ];
    }
}

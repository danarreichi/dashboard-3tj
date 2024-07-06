<?php

namespace App\Http\Requests\Console\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateInventoryRequest extends FormRequest
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
            'name' => ['required', Rule::unique('inventories', 'name')->whereNot('uuid', $this->inventory->uuid)],
            'unit' => ['required'],
            'qty' => ['required', 'numeric', 'min:0']
        ];
    }
}

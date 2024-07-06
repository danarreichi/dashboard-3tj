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
            'code' => ['required', Rule::unique('inventories', 'code')->whereNot('uuid', $this->inventory->uuid), 'min:6', 'max:6'],
            'name' => ['required'],
            'unit' => ['required'],
            'qty' => ['required', 'numeric', 'min:0']
        ];
    }
}

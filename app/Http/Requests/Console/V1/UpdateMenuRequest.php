<?php

namespace App\Http\Requests\Console\V1;

use App\Models\MenuCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateMenuRequest extends FormRequest
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
            'name' => ['required', Rule::unique('menus', 'name')->whereNot('uuid', $this->menu->uuid)],
            'menu_category_id' => ['required', Rule::exists('menu_categories', 'uuid')],
            'image' => ['file', 'mimes:png,jpg']
        ];
    }

    protected function passedValidation()
    {
        $this->merge(['menu_category_id' => MenuCategory::where('uuid', $this->menu_category_id)->first()->id]);
    }
}

<?php

namespace App\Http\Requests\Console\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
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
        abort_if(Auth::user()->userRole->id === 'user', 403, __("User can't add account data"));
        return [
            'username' => ['required', Rule::unique('users', 'username')],
            'name' => ['required'],
            'user_role_id' => ['required', Rule::exists('user_roles', 'id')],
            'password' => ['required']
        ];
    }
}

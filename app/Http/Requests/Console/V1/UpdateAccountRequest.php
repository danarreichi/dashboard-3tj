<?php

namespace App\Http\Requests\Console\V1;

use App\Rules\Console\V1\ChangeUserRoleRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateAccountRequest extends FormRequest
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
            'username' => ['required', Rule::unique('users', 'username')->whereNot('uuid', $this->account->uuid)],
            'name' => ['required'],
            'user_role_id' => ['required', Rule::exists('user_roles', 'id'), new ChangeUserRoleRules(Auth::user(), $this->account)],
            'password' => ['nullable']
        ];
    }
}

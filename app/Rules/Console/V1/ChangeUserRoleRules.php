<?php

namespace App\Rules\Console\V1;

use Illuminate\Contracts\Validation\Rule;

class ChangeUserRoleRules implements Rule
{
    private $loginedUser, $toEditedData, $errorMessage;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($loginedUser, $toEditedData)
    {
        $this->loginedUser = $loginedUser;
        $this->toEditedData = $toEditedData;
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
        if($this->loginedUser->userRole->id === 'user' && ($this->loginedUser->id !== $this->toEditedData->id)) {
            $this->errorMessage = "Pengguna ini tidak dapat mengubah peran orang lain";
            return false;
        }
        if($this->loginedUser->userRole->id === 'user' && $value === 'admin') {
            $this->errorMessage = "Pengguna ini tidak dapat mengubah peran, harap menghubungi Admin untuk perubahan data";
            return false;
        }
        if($this->loginedUser->userRole->id === 'admin' && $value === 'user' && (($this->loginedUser->id === $this->toEditedData->id))) {
            $this->errorMessage = "Admin tidak dapat menurunkan diri ke peran Pengguna";
            return false;
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

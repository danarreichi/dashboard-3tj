<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function authorization(array $attributes)
    {
        $user = parent::index()
            ->where('username', $attributes['username'])
            ->first();
        abort_if(!($user), 401, __('User tidak ditemukan'));
        abort_if(!(Hash::check($attributes['password'], $user->password)), 401, __('User tidak ditemukan'));
        $token = $user->createToken('personal-access-token')->plainTextToken;
        return $token;
    }
}

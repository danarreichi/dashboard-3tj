<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repositories\LoginRepository;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $repository;

    public function __construct(LoginRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProfile()
    {
        return response()->json(['user' => Auth::user()]);
    }

    public function authorization(LoginRequest $request)
    {
        $token = $this->repository->authorization($request->validated());
        return response()->json([
            'token' => $token
        ]);
    }
}

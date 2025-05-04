<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\loginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function login(loginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !\Hash::check($request->password, $user->password)) {
            return ApiResponse::error(__('auth.failed'), Response::HTTP_UNAUTHORIZED);
        }

        $user->auth = $user->createToken('auth_token', ['*'], now()->addDay());
        return ApiResponse::success(LoginResource::make($user), __('auth.login_success'), Response::HTTP_OK);
    }

    public function logout()
    {
        if (!auth()->user()) {
            return ApiResponse::error(__('auth.failed'), Response::HTTP_UNAUTHORIZED);
        }
        auth()->user()->currentAccessToken()->delete();
        return ApiResponse::success(null, __('auth.logout_success'), Response::HTTP_OK);
    }
}

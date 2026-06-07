<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'department' => $request->department,
            'phone'      => $request->phone,
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->success([
            'user'  => $user,
            'token' => $token,
        ], 'Registered successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        }

        $user = JWTAuth::user();

        if (!$user->is_active) {
            return $this->error('Account disabled', 403);
        }

        return $this->success([
            'user'       => $user,
            'token'      => $token,
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->success(null, 'Logged out');
    }

    public function me()
    {
        return $this->success(JWTAuth::user());
    }

    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        return $this->success(['token' => $token]);
    }
}
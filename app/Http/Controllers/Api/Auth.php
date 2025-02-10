<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;

class Auth extends Controller
{

    public function register(RegisterRequest $request)
    {
        // Validate Request
        $validated = $request->validated();

        // Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!$user) {
            return ResponseHelper::error('Something went wrong', 500);
        }

        return ResponseHelper::success($user, 'User created successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        // Validate Request
        $validated = $request->validated();

        // Check User
        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return ResponseHelper::error('Invalid credentials', 401);
        }

        // Create Token JWT
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return ResponseHelper::error('Invalid credentials', 401);
        }

        // Get the authenticated user.
        $user = auth()->user();

        // (optional) Attach the role to the token.
        $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);

        return ResponseHelper::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    public function logout()
    {
        auth()->invalidate(auth()->getToken());
        return ResponseHelper::success([], 'Logout successfully');
    }

    public function refresh()
    {
        return ResponseHelper::success([
            'access_token' => auth()->refresh(),
            'token_type' => 'Bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return ResponseHelper::error('User not found', 404);
            }
        } catch (JWTException $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('Invalid token', 401);
        }

        return ResponseHelper::success($user);
    }
}

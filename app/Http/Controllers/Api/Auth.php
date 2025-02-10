<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;

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

        if (!$token = FacadesAuth::attempt($credentials)) {
            return ResponseHelper::error('Invalid credentials', 401);
        }

        return ResponseHelper::success([
            'access_token' => $token,
            'refresh_token' => FacadesAuth::refresh()
        ]);
    }
}

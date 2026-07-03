<?php

namespace App\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends AppController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = $this->authService->register($validated);
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $token = $this->authService->login($validated);

        return $this->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->json(['message' => 'Logged out.']);
    }

    public function me(Request $request)
    {
        return $this->json([
            'id' => $request->user()->id,
            'email' => $request->user()->email,
        ]);
    }
}

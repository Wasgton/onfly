<?php

namespace App\Http\Controllers;

use App\Exceptions\FailToCreateException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Service\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService){}

    /**
     * @throws FailToCreateException
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $this->authService->createUser($validated);
        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $response = $this->authService->login($credentials);
        return response()->json($response);
    }

}

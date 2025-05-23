<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthServiceInterface;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\AuthenticationFailedException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Function to create a new user account
     *
     * @author Payal
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            ['user' => $user, 'token' => $token] = $this->authService->register($request->validated());
            return response()->json(compact('user', 'token'), Response::HTTP_CREATED);
        } catch (UserAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Processes login request and returns auth token
     *
     * @author Payal
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            ['user' => $user, 'token' => $token] = $this->authService->login($request->validated());
            return response()->json(compact('user', 'token'), Response::HTTP_OK);
        } catch (AuthenticationFailedException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }
}

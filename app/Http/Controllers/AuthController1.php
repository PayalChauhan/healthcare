<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Constants\AuthConstants;

class AuthController extends Controller
{
    /**
     * Function to create a new user account
     *
     * @author Payal
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->exists()) {
            return response()->json([
                'message' => AuthConstants::USER_EXIST,
            ], AuthConstants::HTTP_CONFLICT);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], AuthConstants::HTTP_CREATED);
    }

    /**
     * Processes login request and returns auth token
     *
     * @author Payal
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!auth()->attempt($credentials)) {
            return response()->json(
                [
                    'message' => AuthConstants::MSG_INVALID_CREDENTIALS
                ],
                AuthConstants::HTTP_UNAUTHORIZED
            );
        }
        $user = auth()->user();
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(
            [
                'user' => $user,
                'token' => $token
            ],
            AuthConstants::HTTP_OK
        );
    }
}

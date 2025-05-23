<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Constants\AuthConstants;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\AuthenticationFailedException;

class AuthService implements AuthServiceInterface
{
    /**
     * service to create a new user account
     *
     * @author Payal
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        if (User::where('email', $data['email'])->exists()) {
            throw new UserAlreadyExistsException(
                AuthConstants::USER_EXIST,
                AuthConstants::HTTP_CONFLICT
            );
        }
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $token = $user->createToken('api-token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    /**
     * Service for login request and returns auth token
     *
     * @author Payal
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        if (! auth()->attempt($credentials)) {
            throw new AuthenticationFailedException(
                AuthConstants::MSG_INVALID_CREDENTIALS,
                AuthConstants::HTTP_UNAUTHORIZED
            );
        }
        $user = auth()->user();
        $token = $user->createToken('api-token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }
}

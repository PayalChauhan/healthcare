<?php

namespace App\Services;

interface AuthServiceInterface
{
    /**
     * Register a new user and return token
     *
     * @author payal
     * @param array $data
     * @return array
     */
    public function register(array $data): array;

    /**
     * Authenticate credentials and return token
     *
     * @author payal
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array;
}

<?php

namespace App\Constants;

class AuthConstants
{
    //message
    public const MSG_INVALID_CREDENTIALS = 'Invalid credentials';
    public const USER_EXIST = "User already registered";

    //HTTP status
    public const HTTP_CONFLICT = 409;
    public const HTTP_CREATED = 201;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_OK = 200;
    public const HTTP_FORBIDDEN = 403;
}

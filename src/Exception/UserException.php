<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class UserException extends ApiException
{
    public static function invalidUserRole(): UserException
    {
        return new static("INVALID_USER_ROLE", Response::HTTP_BAD_REQUEST);
    }

    public static function emailAlreadyExists(): UserException
    {
        return new static("EMAIL_ALREADY_EXISTS", Response::HTTP_BAD_REQUEST);
    }

    public static function usernameAlreadyExists(): UserException
    {
        return new static("USERNAME_ALREADY_EXISTS" , Response::HTTP_BAD_REQUEST);
    }

    public static function userNotFound(): UserException
    {
        return new static("USER_NOT_FOUND" , Response::HTTP_NOT_FOUND);
    }
}

<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class ApiException extends \Exception
{
    public static function wrongEmail(): ApiException
    {
        return new static("INCORRECT_EMAIL", Response::HTTP_BAD_REQUEST);
    }

    public static function incorectRequest(): ApiException
    {
        return new static("INCORRECT_REQUEST", Response::HTTP_BAD_REQUEST);
    }

    public static function accessDenied(): ApiException
    {
        return new static("ACCESS_DENIED", Response::HTTP_FORBIDDEN);
    }
}

<?php
declare(strict_types=1);

namespace App\Validator;

interface UniqueUsernameValidator
{
    public function isUsernameUnique(string $username): bool;
}

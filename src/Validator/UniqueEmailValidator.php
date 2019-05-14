<?php
declare(strict_types=1);

namespace App\Validator;

interface UniqueEmailValidator
{
    public function isEmailUnique(string $email): bool;
}

<?php
declare(strict_types=1);

namespace App\Entity;

class Roles
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public static function availableRoles(): array
    {
        return [
            self::ROLE_USER,
            self::ROLE_ADMIN,
            self::ROLE_SUPER_ADMIN
        ];
    }

    public static function getDefaultRoles(): array
    {
        return [self::ROLE_SUPER_ADMIN];
    }

    public static function isValid(string $value): bool
    {
        return array_search($value, self::availableRoles()) === false ? false : true;
    }
}

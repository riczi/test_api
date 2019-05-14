<?php
declare(strict_types=1);

namespace App\Validator;

use App\Exception\UserException;
use App\Repository\UserRepository;

class UserValidator implements UniqueEmailValidator, UniqueUsernameValidator
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isEmailUnique(string $email): bool
    {
        $results = $this->userRepository->findOneByEmail($email);

        if ($results) {
            throw UserException::emailAlreadyExists();
        }

        return true;
    }

    public function isUsernameUnique(string $username): bool
    {
        $results = $this->userRepository->findOneByUsername($username);

        if ($results) {
            throw UserException::usernameAlreadyExists();
        }

        return true;
    }

    public function checkUniqueUser(string $username, ?string $email = null): bool
    {
        if ($username) {
            $usernameUnique = $this->isUsernameUnique($username);
        }

        if ($email) {
            $emailUnique = $this->isEmailUnique($email);
        }

        return ($emailUnique && $usernameUnique) ? true : false;
    }
}

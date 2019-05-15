<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\ApiException;
use App\Repository\UserRepository;
use App\Validator\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserValidator */
    private $validator;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserValidator $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function delete(string $id): JsonResponse
    {
        $user = $this->userRepository->findUserById(Uuid::fromString($id));

        $this->checkAccessToOwnerOrAdmin($user->getId());

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    public function get(string $id): JsonResponse
    {
        $user = $this->userRepository->findUserById(Uuid::fromString($id));

        $this->checkAccessToOwnerOrAdmin($user->getId());

        return new JsonResponse(
            [
                'item' => $user
            ],
            JsonResponse::HTTP_OK
        );
    }

    public function cget(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userRepository->findAll();

        return new JsonResponse(
            [
                'count' => count($users),
                'items' => $users
            ],
            JsonResponse::HTTP_OK
        );
    }

    public function post(Request $request)
    {
        $data = json_decode($request->getContent(),true);

        if (!isset($data['username']) || !isset($data['password']) ||  !isset($data['email'])) {
            throw ApiException::incorectRequest();
        }

        $email = $data['email'];
        $username = $data['username'];
        $password = $data['password'];

        $this->validator->checkUniqueUser($username, $email);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'item' => $user,
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    public function put(Request $request, string $id)
    {
        $data = json_decode($request->getContent(),true);

        if (!isset($data['username']) ||  !isset($data['email'])) {
            throw ApiException::incorectRequest();
        }

        $email = $data['email'];
        $username = $data['username'];
        $password = isset($data['password']) ? $data['password'] : null;

        $user = $this->userRepository->findUserById(Uuid::fromString($id));

        $this->checkAccessToOwnerOrAdmin($user->getId());

        if ($user->getUsername() != $username && $this->validator->isUsernameUnique($username)) {
            $user->setUserName($username);
        }

        if ($user->getEmail() != $email  && $this->validator->isEmailUnique($email)) {
            $user->setEmail($email);
        }

        if ($password) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'item' => $user,
            ],
            JsonResponse::HTTP_OK
        );
    }

    public function patch(Request $request, string $id)
    {
        $data = json_decode($request->getContent(),true);

        $user = $this->userRepository->findUserById(Uuid::fromString($id));

        $this->checkAccessToOwnerOrAdmin($user->getId());

        if (isset($data['username']) && $user->getUsername() != $data['username'] && $this->validator->isUsernameUnique($data['username'])) {
            $user->setUserName($data['username']);
        }

        if (isset($data['email'])  && $user->getEmail() != $data['email']  && $this->validator->isEmailUnique($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['password'])) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'item' => $user,
            ],
            JsonResponse::HTTP_OK
        );
    }

    private function checkAccessToOwnerOrAdmin(UuidInterface $id): bool
    {
        if($id != $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN')) {
            throw ApiException::accessDenied();
        }

        return true;
    }
}

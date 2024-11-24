<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\UserDTO;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserEntityBuilder
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    )
    {
    }

    public function buildFromDTO(UserDTO $dto): User
    {
        $user = new User();

        $user
            ->setRoles($dto->roles)
            ->setEmail($dto->email)
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName);

        $user->setPassword(
            $this->hasher->hashPassword($user, $dto->password)
        );

        return $user;
    }

    public function updateFromDTO(User $user, UserDTO $dto): void
    {
        $user
            ->setRoles($dto->roles ?? $user->getRoles())
            ->setEmail($dto->email ?? $user->getEmail())
            ->setFirstName($dto->firstName ?? $user->getFirstName())
            ->setLastName($dto->lastName ?? $user->getLastName());

        if (isset($dto->password)) {
            $user->setPassword(
                $this->hasher->hashPassword($user, $dto->password)
            );
        }
    }
}

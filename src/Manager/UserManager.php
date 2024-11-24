<?php

declare(strict_types=1);

namespace App\Manager;

use App\Builder\UserEntityBuilder;
use App\DTO\UserDTO;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserEntityBuilder      $userEntityBuilder,
        private LoggerInterface        $logger
    )
    {
    }

    public function saveUser(UserDTO $userDTO): User
    {
        $user = $this->userEntityBuilder->buildFromDTO($userDTO);
        $this->em->persist($user);
        $this->em->flush();

        $this->logger->info('User has been created', [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getFirstName() . ' ' . $user->getLastName(),
            'time' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        return $user;
    }

    public function update(User $user, UserDTO $userDTO): void
    {
        $this->userEntityBuilder->updateFromDTO($user, $userDTO);
        $this->em->flush();
    }
}

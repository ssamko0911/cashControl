<?php

declare(strict_types=1);

namespace App\Manager;

use App\Builder\AccountEntityBuilder;
use App\DTO\AccountDTO;
use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;

final readonly class AccountManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private AccountEntityBuilder $builder,
    ) {
    }

    public function save(AccountDTO $dto): Account
    {
        $account = $this->builder->buildFromDTO($dto);
        $this->em->persist($account);
        $this->em->flush();

        return $account;
    }
}
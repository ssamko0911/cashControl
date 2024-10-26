<?php

declare(strict_types=1);

namespace App\Manager;

use App\Builder\AccountEntityBuilder;
use App\DTO\AccountDTO;
use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class AccountManager
{
    public function __construct(
        private EntityManagerInterface   $em,
        private AccountEntityBuilder     $accountEntityBuilder,
        private LoggerInterface          $logger,
    )
    {
    }

    public function saveAccount(AccountDTO $dto): Account
    {
        $account = $this->accountEntityBuilder->buildFromDTO($dto);
        $this->em->persist($account);
        $this->em->flush();

        $this->logger->info('Account has been created', [
            'id' => $account->getId(),
            'name' => $account->getName(),
            'time' => $account->getCreatedAt(),
        ]);

        return $account;
    }
}

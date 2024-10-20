<?php

declare(strict_types=1);

namespace App\Manager;

use App\Builder\AccountEntityBuilder;
use App\Builder\TransactionEntityBuilder;
use App\DTO\AccountDTO;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class AccountManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private AccountEntityBuilder $accountEntityBuilder,
        private TransactionEntityBuilder $transactionEntityBuilder,
        private LoggerInterface $logger
    ) {
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

    public function saveTransaction(TransactionDTO $dto, int $categoryId, Account $account): Transaction
    {
        $transaction = $this->transactionEntityBuilder->buildFromDTO($dto, $categoryId, $account);
        $this->em->persist($transaction);
        $this->em->flush();

        $this->logger->info('Transaction has been created', [
            'id' => $transaction->getId(),
            'description' => $transaction->getDescription(),
            'account id' => $transaction->getAccount()->getId(),
            'time' => $transaction->getCreatedAt(),
        ]);

        return $transaction;
    }
}
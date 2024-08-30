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

final readonly class AccountManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private AccountEntityBuilder $accountEntityBuilder,
        private TransactionEntityBuilder $transactionEntityBuilder
    ) {
    }

    public function saveAccount(AccountDTO $dto): Account
    {
        $account = $this->accountEntityBuilder->buildFromDTO($dto);
        $this->em->persist($account);
        $this->em->flush();

        return $account;
    }

    public function saveTransaction(TransactionDTO $dto, Account $account): Transaction
    {
        $transaction = $this->transactionEntityBuilder->buildFromDTO($dto, $account);
        $this->em->persist($transaction);
        $this->em->flush();

        return $transaction;
    }
}
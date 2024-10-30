<?php

namespace App\Manager;

use App\Builder\TransactionEntityBuilder;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Service\AccountService;
use App\Service\CategoryBudgetService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class TransactionManager
{
    public function __construct(
        private EntityManagerInterface   $em,
        private TransactionEntityBuilder $transactionEntityBuilder,
        private LoggerInterface          $logger,
        private CategoryBudgetService    $categoryBudgetService,
        private AccountService           $accountService,
    )
    {
    }

    public function saveTransaction(TransactionDTO $transactionDTO, int $categoryId, Account $account): Transaction
    {
        $transaction = $this->transactionEntityBuilder->buildFromDTO($transactionDTO, $categoryId, $account);
        $this->em->persist($transaction);

        $this->categoryBudgetService->update($transaction, $categoryId);
        $this->accountService->update($account, $transactionDTO);
        $this->em->flush();

        $this->logger->info('Transaction has been created', ['id' => $transaction->getId(),
            'description' => $transaction->getDescription(),
            'account id' => $transaction->getAccount()->getId(),
            'time' => $transaction->getCreatedAt(),]);

        return $transaction;
    }
}

<?php

declare(strict_types=1);

namespace App\Manager;

use App\Builder\AccountEntityBuilder;
use App\Builder\TransactionEntityBuilder;
use App\DTO\AccountDTO;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\CategoryBudget;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use App\Repository\CategoryBudgetRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Psr\Log\LoggerInterface;

final readonly class AccountManager
{
    public function __construct(
        private EntityManagerInterface   $em,
        private AccountEntityBuilder     $accountEntityBuilder,
        private TransactionEntityBuilder $transactionEntityBuilder,
        private LoggerInterface          $logger,
        private TransactionRepository    $transactionRepository,
        private CategoryBudgetRepository $categoryBudgetRepository
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

    public function saveTransaction(TransactionDTO $dto, int $categoryId, Account $account): Transaction
    {
        $transaction = $this->transactionEntityBuilder->buildFromDTO($dto, $categoryId, $account);
        $this->em->persist($transaction);
        $this->em->flush();

        /** @var Transaction|null $previousTransaction */
        $previousTransaction = $this->transactionRepository->find($transaction->getId() - 1);
        $currentCategoryBudget = $this->categoryBudgetRepository->findBy(['category' => $categoryId]);

        if (null !== $previousTransaction &&
            ($previousTransaction->getCreatedAt()->format('n') !== $transaction->getCreatedAt()->format('n'))) {
            $categoryBudget = new CategoryBudget();
            $amount = $dto->amount->amount;
            $currency = new Currency($dto->amount->currency->code);
            $categoryBudget->setCurrentSpending(new Money($amount, $currency));
            $this->updateCategoryBudget($categoryBudget, $transaction);

        }


        $this->logger->info('Transaction has been created', ['id' => $transaction->getId(),
            'description' => $transaction->getDescription(),
            'account id' => $transaction->getAccount()->getId(),
            'time' => $transaction->getCreatedAt(),]);

        return $transaction;
    }

    private function updateCategoryBudget(CategoryBudget $categoryBudget, Transaction $transaction): void
    {
        if ($transaction->getType() === TransactionType::TYPE_EXPENSE) {
            $newAmount = $categoryBudget->getCurrentSpending()->add($transaction->getAmount());
        } else {
            $newAmount = $categoryBudget->getCurrentSpending()->subtract($transaction->getAmount());
        }

        $categoryBudget->setCurrentSpending($newAmount);

        if ($newAmount->compare($categoryBudget->getBudgetLimit()) >= 0) {
            $categoryBudget->setIsOverBudget(true);
        }
    }

    public function isNextMonth(\DateTimeImmutable $date1, \DateTimeImmutable $date2): bool
    {
        $nextMonth = (clone $date1)->modify('first day of next month');
        $lastDayOfNextMonth = (clone $nextMonth)->modify('last day of this month');

        return $date2 >= $nextMonth && $date2 <= $lastDayOfNextMonth;
    }


}
<?php

namespace App\Manager;

use App\Builder\TransactionEntityBuilder;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Category;
use App\Entity\CategoryBudget;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use App\Repository\CategoryBudgetRepository;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Psr\Log\LoggerInterface;

final readonly class TransactionManager
{
    public function __construct(
        private EntityManagerInterface   $em,
        private TransactionEntityBuilder $transactionEntityBuilder,
        private LoggerInterface          $logger,
        private TransactionRepository    $transactionRepository,
        private CategoryRepository       $categoryRepository
    )
    {
    }

    public function saveTransaction(TransactionDTO $transactionDTO, int $categoryId, Account $account): Transaction
    {
        $transaction = $this->transactionEntityBuilder->buildFromDTO($transactionDTO, $categoryId, $account);
        $this->em->persist($transaction);
        $this->em->flush();

        //smth call;

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

    public function smth(Transaction $transaction, int $categoryId): void
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($categoryId);
        $currentCategoryBudget = $category->getMonthlyBudget();

        $categoryBudget = $this->getCategoryBudget($transaction, $currentCategoryBudget);

        $this->updateCategoryBudget($categoryBudget, $transaction);
    }

    private function getCategoryBudget(Transaction $transaction, ?CategoryBudget $currentCategoryBudget): CategoryBudget
    {
        if (null === $currentCategoryBudget) {
            return new CategoryBudget();
        } else {
            $currentCategoryBudgetMonth = $currentCategoryBudget->getMonthYear();
            $transactionMonthYear = $transaction->getCreatedAt()->format("m Y");

            if ($currentCategoryBudgetMonth !== $transactionMonthYear) {
                return new CategoryBudget();
            }
        }

        return $currentCategoryBudget;
    }
}
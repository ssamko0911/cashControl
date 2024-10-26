<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryBudget;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use App\Repository\CategoryRepository;

final readonly class CategoryBudgetService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    )
    {
    }

    public function smth(Transaction $transaction, int $categoryId): void
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($categoryId);
        $currentCategoryBudget = $category->getMonthlyBudget();

        $newCategoryBudget = $this->getOrCreate($transaction, $currentCategoryBudget);

        $this->updateCategoryBudget($newCategoryBudget, $transaction);
    }

    private function getOrCreate(Transaction $transaction, ?CategoryBudget $currentCategoryBudget): CategoryBudget
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
}
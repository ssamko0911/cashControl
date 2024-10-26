<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryBudget;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Money\Money;

final readonly class CategoryBudgetService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    )
    {
    }

    public function update(Transaction $transaction, int $categoryId): void
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($categoryId);
        $currentCategoryBudget = $category->getMonthlyBudget();

        $newCategoryBudget = $this->getOrCreate($transaction, $category, $currentCategoryBudget);

        $this->updateCurrentSpending($newCategoryBudget, $transaction);
    }

    private function getOrCreate(Transaction $transaction, Category $category, ?CategoryBudget $currentCategoryBudget): CategoryBudget
    {
        if (null === $currentCategoryBudget) {
            return $this->create($category);
        } else {
            $currentCategoryBudgetMonth = $currentCategoryBudget->getMonthYear();
            $transactionMonthYear = $transaction->getCreatedAt()->format("m Y");

            if ($currentCategoryBudgetMonth !== $transactionMonthYear) {
                return $this->create($category);
            }
        }

        return $currentCategoryBudget;
    }

    private function updateCurrentSpending(CategoryBudget $categoryBudget, Transaction $transaction): void
    {
        if ($transaction->getType() === TransactionType::TYPE_EXPENSE) {
            $newAmount = $categoryBudget->getCurrentSpending()->add($transaction->getAmount());
        } else {
            $newAmount = $categoryBudget->getCurrentSpending()->subtract($transaction->getAmount());
        }

        $categoryBudget->setCurrentSpending($newAmount);
        $this->setIsOverBudget($newAmount, $categoryBudget);
    }

    private function setIsOverBudget(Money $newAmount, CategoryBudget $categoryBudget): void
    {
        if ($newAmount->compare($categoryBudget->getBudgetLimit()) >= 0) {
            $categoryBudget->setIsOverBudget(true);
        }
    }

    private function create(Category $category): CategoryBudget
    {
        return (new CategoryBudget())
            ->setCategory($category)
            ->setBudgetLimit(null)
            ->setMonthYear((new DateTimeImmutable())->format('m Y'));
    }
}

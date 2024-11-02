<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryBudget;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use App\Repository\CategoryBudgetRepository;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;

final readonly class CategoryBudgetService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private CategoryBudgetRepository $categoryBudgetRepository,
        private EntityManagerInterface $em,
    )
    {
    }

    public function update(Transaction $transaction, int $categoryId): void
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($categoryId);
        $currentCategoryBudget = $this->categoryBudgetRepository->findOneBy(['monthYear' => (new DateTimeImmutable())->format('F Y')]);

        $newCategoryBudget = $this->getOrCreate($transaction, $category, $currentCategoryBudget);

        $this->updateCurrentSpending($newCategoryBudget, $transaction);
    }

    private function getOrCreate(Transaction $transaction, Category $category, ?CategoryBudget $currentCategoryBudget): CategoryBudget
    {
        if (null === $currentCategoryBudget) {
            return $this->create($category);
        }

        $currentCategoryBudgetMonth = $currentCategoryBudget->getMonthYear();
        $transactionMonthYear = $transaction->getCreatedAt()->format("F Y");

        if ($currentCategoryBudgetMonth !== $transactionMonthYear) {
            return $this->create($category);
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
    }

    private function create(Category $category): CategoryBudget
    {
        $budget =  new CategoryBudget();

        $money = new Money('0', new Currency('UAH'));

        $budget
            ->setBudgetLimit(null)
            ->setCurrentSpending($money)
            ->setMonthYear((new DateTimeImmutable())->format('F Y'));

        $category->addMonthlyBudget($budget);

        $this->em->persist($budget);
        $this->em->flush();

        return $budget;
    }
}

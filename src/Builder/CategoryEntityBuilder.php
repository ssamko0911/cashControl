<?php

namespace App\Builder;

use App\DTO\CategoryDTO;
use App\Entity\Category;
use App\Entity\CategoryBudget;
use DateTimeImmutable;
use LogicException;
use Money\Currency;
use Money\Money;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class CategoryEntityBuilder
{
    public function __construct(
        private ParameterBagInterface $params)
    {
    }

    public function buildFromDTO(CategoryDTO $dto): Category
    {
        $category = (new Category())
            ->setName($dto->name)
            ->setDescription($dto->description);

        $category->addMonthlyBudget($this->getMonthlyBudget($dto, $category));

        return $category;
    }

    private function getMonthlyBudget(CategoryDTO $categoryDTO, Category $category): CategoryBudget
    {
        if (count($categoryDTO->monthlyBudgets) > 1) {
            throw new LogicException('Expecting limit for current month');
        }

        $budgetDTO = current($categoryDTO->monthlyBudgets);

        return (new CategoryBudget())
            ->setCategory($category)
            ->setIsOverBudget(false)
            ->setLimit($budgetDTO->limit)
            ->setMonthYear((new DateTimeImmutable()))
            ->setCurrentSpending(new Money('0', new Currency(
                $this->params->get('default_currency')
            )));
    }
}
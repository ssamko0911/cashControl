<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\CategoryBudgetDTO;
use App\DTO\CategoryDTO;
use App\DTO\CurrencyDTO;
use App\DTO\MoneyDTO;
use App\Entity\Category;
use App\Entity\CategoryBudget;
use App\Manager\AutoMapper;
use App\Security\AccessGroup;
use DateTimeImmutable;
use LogicException;
use Money\Currency;
use Money\Money;
use Random\RandomException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class CategoryEntityBuilder
{
    public function __construct(
        #[Autowire('%default_currency%')]
        private string              $defaultCurrency,
        private readonly AutoMapper $mapper
    )
    {

    }

    public function buildFromDTO(CategoryDTO $dto): Category
    {
        $category = (new Category())
            ->setName($dto->name)
            ->setDescription($dto->description);

        $this->setDefaultMonthlyBudget($category);

        return $category;
    }

    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    public function buildDTO(Category $category): CategoryDTO
    {
        $categoryDTO = new CategoryDTO();

        $categoryDTO->name = $category->getName();
        $categoryDTO->description = $category->getDescription();

        $budgets = $category->getMonthlyBudgets();
        $budgetDTOs = [];

        foreach ($budgets as $budget) {
            $budgetDTOs[] = $this->mapper->mapToModel($budget, AccessGroup::CATEGORY_READ);
        }

        $categoryDTO->monthlyBudgets = $budgetDTOs;

        return $categoryDTO;
    }

    private function setDefaultMonthlyBudget(Category $category): void
    {
        $defaultBudget = new CategoryBudget();

        $defaultBudget
            ->setBudgetLimit(null)
            ->setCurrentSpending(new Money('0', new Currency(
                $this->defaultCurrency
            )));

        $category->addMonthlyBudget($defaultBudget);
    }
}

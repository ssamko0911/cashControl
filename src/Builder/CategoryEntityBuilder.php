<?php

namespace App\Builder;

use App\DTO\CategoryBudgetDTO;
use App\DTO\CategoryDTO;
use App\DTO\CurrencyDTO;
use App\DTO\MoneyDTO;
use App\Entity\Category;
use App\Entity\CategoryBudget;
use DateTimeImmutable;
use LogicException;
use Money\Currency;
use Money\Money;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class CategoryEntityBuilder
{
    private string $defaultCurrency;

    public function __construct(private ParameterBagInterface $params)
    {
        $this->defaultCurrency = $this->params->get('default_currency');
    }

    public function buildFromDTO(CategoryDTO $dto): Category
    {
        $category = (new Category())
            ->setName($dto->name)
            ->setDescription($dto->description);

        if (isset($dto->monthlyBudget)) {
            $category->setMonthlyBudget($this->getMonthlyBudget($dto));
        }

        return $category;
    }

    public function buildDTO(Category $category): CategoryDTO
    {
        $categoryDTO = new CategoryDTO();

        $categoryDTO->name = $category->getName();
        $categoryDTO->description = $category->getDescription();

        $budget = $category->getMonthlyBudget();

        if (null !== $budget) {
            $categoryBudgetDTO = new CategoryBudgetDTO();
            $categoryBudgetDTO->isOverBudget = $budget->isOverBudget();

            $budgetLimit = new MoneyDTO();
            $budgetLimit->amount = $budget->getBudgetLimit()->getAmount();

            $currency = new CurrencyDTO();
            $currency->code = $budget->getBudgetLimit()->getCurrency()->getCode();

            $budgetLimit->currency = $currency;
            $categoryBudgetDTO->budgetLimit = $budgetLimit;

            $categoryBudgetDTO->monthYear = $budget->getMonthYear();

            $currentSpending = new MoneyDTO();
            $currentSpending->amount = $budget->getCurrentSpending()->getAmount();

            $currency = new CurrencyDTO();
            $currency->code = $budget->getCurrentSpending()->getCurrency()->getCode();

            $currentSpending->currency = $currency;
            $categoryBudgetDTO->currentSpending = $currentSpending;

            $categoryDTO->monthlyBudget = $categoryBudgetDTO;
        }

        return $categoryDTO;
    }

    private function getMonthlyBudget(CategoryDTO $categoryDTO): CategoryBudget
    {
        $budgetDTO = $categoryDTO->monthlyBudget;

        $budgetLimit = new Money(
            $budgetDTO->budgetLimit->amount,
                new Currency($budgetDTO->budgetLimit->currency->code)
        );

        return (new CategoryBudget())
            ->setBudgetLimit($budgetLimit)
            ->setCurrentSpending(new Money('0', new Currency(
                $this->defaultCurrency
            )));
    }
}

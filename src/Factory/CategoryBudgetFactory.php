<?php

namespace App\Factory;

use App\Entity\Category;
use App\Entity\CategoryBudget;
use Money\Currency;
use Money\Money;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CategoryBudgetFactory extends PersistentProxyObjectFactory
{

    protected function defaults(): array|callable
    {
        return [
            'limit' => $this->getMoney(),
            'currentSpending' => $this->getMoney(),
        ];
    }

    protected function getMoney(): Money
    {
        return new Money(
            self::faker()->randomNumber(3),
            new Currency(
                self::faker()->currencyCode()
            )
        );
    }

    public static function class(): string
    {
        return CategoryBudget::class;
    }
}

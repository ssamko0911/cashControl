<?php

namespace App\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CategoryFactory extends PersistentProxyObjectFactory
{

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->unique()->word(),
            'description' => self::faker()->text(100),
            'monthlyBudget' => CategoryBudgetFactory::createOne()
        ];
    }

    public static function class(): string
    {
        return Category::class;
    }
}

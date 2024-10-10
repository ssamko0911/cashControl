<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\CategoryBudgetFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryBudgetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CategoryBudgetFactory::createMany(10);

        $manager->flush();
    }
}

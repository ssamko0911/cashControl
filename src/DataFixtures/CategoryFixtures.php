<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CategoryBudget;
use App\Factory\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        CategoryFactory::createMany(10);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryBudgetFixtures::class,
        ];
    }
}

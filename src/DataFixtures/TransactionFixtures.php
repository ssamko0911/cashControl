<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\AccountFactory;
use App\Factory\CategoryFactory;
use App\Factory\TransactionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        TransactionFactory::new()->many(50)->create(
            fn() => [
                'account' => AccountFactory::random(),
                'category' => CategoryFactory::random(),
            ]
        );

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            CategoryFixtures::class,
        ];
    }
}

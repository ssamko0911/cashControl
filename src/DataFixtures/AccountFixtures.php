<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\AccountFactory;
use App\Factory\TransactionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Random\RandomException;

class AccountFixtures extends Fixture
{
    /**
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        $accounts = AccountFactory::createMany(10);

        foreach ($accounts as $account) {
            $transactionCount = random_int(1, 5);
            TransactionFactory::createMany($transactionCount, [
                'account' => $account,
            ]);
        }

        $manager->flush();
    }
}

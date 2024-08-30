<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\AccountFactory;
use App\Factory\TransactionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AccountFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $accounts = AccountFactory::createMany(10);

        $transactions = TransactionFactory::createMany(40);

        foreach ($transactions as $transaction) {
            $transaction->object()->setAccount(AccountFactory::randomOrCreate()->object());
        }

        $manager->flush();
    }
}

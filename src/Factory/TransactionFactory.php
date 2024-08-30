<?php

namespace App\Factory;

use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use Money\Currency;
use Money\Money;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class TransactionFactory extends PersistentProxyObjectFactory
{

    protected function defaults(): array|callable
    {
        return [
            'amount' => $this->getMoney(),
            'description' => self::faker()->text(100),
            'type' => self::faker()->randomElement(TransactionType::cases()),
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
        return Transaction::class;
    }
}
<?php

namespace App\Factory;

use App\Entity\Account;
use App\Entity\Enum\AccountTypeEnum;
use Money\Currency;
use Money\Money;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class AccountFactory extends PersistentProxyObjectFactory
{

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->unique()->word,
            'accountType' => self::faker()->randomElement(AccountTypeEnum::cases()),
            'description' => self::faker()->text(100),
            'total' => $this->getMoney(),
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
        return Account::class;
    }
}

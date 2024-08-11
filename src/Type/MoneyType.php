<?php

declare(strict_types=1);

namespace App\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Money\Currency;
use Money\Money;

class MoneyType extends Type
{
    const string MONEY = 'money'; // Custom type name

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof Money) {
            return $value;
        }

        $data = json_decode($value, true);
        return new Money($data['amount'], new Currency($data['currency']));
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Money) {
            throw new InvalidArgumentException('Expected ' . Money::class . ', got ' . gettype($value));
        }

        return json_encode([
            'amount' => $value->getAmount(),
            'currency' => $value->getCurrency()->getCode(),
        ]);
    }

    public function getName(): string
    {
        return self::MONEY;
    }
}
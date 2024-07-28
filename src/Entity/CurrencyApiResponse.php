<?php declare(strict_types=1);

namespace App\Entity;

class CurrencyApiResponse
{
    public string $base_currency_code;
    public string $base_currency_name;
    public string $amount;
    public array $rates;
}
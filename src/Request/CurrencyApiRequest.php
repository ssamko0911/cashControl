<?php declare(strict_types = 1);

namespace App\Request;

class CurrencyApiRequest
{
    public string $fromCurrency;
    public string $toCurrency;
    public float $amount;
}
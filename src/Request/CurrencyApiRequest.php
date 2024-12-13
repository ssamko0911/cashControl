<?php

declare(strict_types=1);

namespace App\Request;

class CurrencyApiRequest
{
    public function __construct(
        public string $fromCurrency,
        public string $toCurrency,
        public float  $amount,
    )
    {
    }
}

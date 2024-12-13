<?php

declare(strict_types=1);

namespace App\Response;

final class CurrencyApiResponse
{
    public function __construct(
        public string $base_currency_code,
        public string $base_currency_name,
        public string $amount,
        /**
         * @var array<string, string[]>
         */
        public array  $rates,
    )
    {
    }
}

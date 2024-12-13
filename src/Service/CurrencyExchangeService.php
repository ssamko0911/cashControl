<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\CurrencyApiClientException;
use App\Request\CurrencyApiRequest;
use Money\Currency;
use Money\Money;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class CurrencyExchangeService
{
    public function __construct(
        #[Autowire('%default_currency%')]
        private string            $defaultCurrency,
        private CurrencyApiClient $currencyApiClient,
    )
    {
    }

    /**
     * @throws CurrencyApiClientException
     */
    public function exchange(Money $money): Money
    {
        if (!$this->isDefaultCurrency($money->getCurrency())) {
            $newAmount = $this->currencyApiClient->get($this->getCurrencyApiRequest($money))->amount;

            return new Money($newAmount, new Currency($this->defaultCurrency));
        }

        return $money;
    }

    private function getCurrencyApiRequest(Money $money): CurrencyApiRequest
    {
        return new CurrencyApiRequest($money->getCurrency()->getCode(), $this->defaultCurrency, (float)$money->getAmount());
    }

    private function isDefaultCurrency(Currency $currency): bool
    {
        return $this->defaultCurrency === $currency->getCode();
    }
}
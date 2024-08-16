<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\AccountDTO;
use App\DTO\CurrencyDTO;
use App\DTO\MoneyDTO;
use App\Entity\Account;
use Money\Currency;
use Money\Money;

final readonly class AccountEntityBuilder
{
    public function buildFromDTO(AccountDTO $dto): Account
    {
        return (new Account())
            ->setDescription($dto->description)
            ->setTotal(
                new Money(
                    $dto->total->amount,
                    new Currency($dto->total->currency->code)
                )
            )
            ->setAccountType($dto->accountType);
    }

    public function buildDTO(Account $account): AccountDTO
    {
        $dto = new AccountDTO();
        $dto->accountType = $account->getAccountType();
        $dto->description = $account->getDescription();

        $currencyDto = new CurrencyDTO();
        $currencyDto->code = $account->getTotal()->getCurrency()->getCode();

        $total = new MoneyDTO();
        $total->amount = $account->getTotal()->getAmount();

        $total->currency = $currencyDto;

        $dto->total = $total;
        $dto->createdAt = $account->getCreatedAt();
        $dto->updatedAt = $account->getUpdatedAt();
        $dto->id = $account->getId();

        return $dto;
    }
}
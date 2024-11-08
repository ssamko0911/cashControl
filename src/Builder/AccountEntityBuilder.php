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
    /**
     * @param AccountDTO $dto
     * @return Account
     */
    public function buildFromDTO(AccountDTO $dto): Account
    {
        return (new Account())
            ->setName($dto->name)
            ->setDescription($dto->description)
            ->setTotal(
                new Money(
                    $dto->total->amount,
                    new Currency($dto->total->currency->code)
                )
            )
            ->setAccountType($dto->accountType);
    }

    /**
     * @param Account $entity
     * @return AccountDTO
     */
    public function buildDTO(Account $entity): AccountDTO
    {
        $dto = new AccountDTO();
        $dto->name = $entity->getName();
        $dto->accountType = $entity->getAccountType();
        $dto->description = $entity->getDescription();

        $currencyDto = new CurrencyDTO();
        $currencyDto->code = $entity->getTotal()->getCurrency()->getCode();

        $total = new MoneyDTO();
        $total->amount = $entity->getTotal()->getAmount();

        $total->currency = $currencyDto;

        $dto->total = $total;
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->id = $entity->getId();

        return $dto;
    }
}
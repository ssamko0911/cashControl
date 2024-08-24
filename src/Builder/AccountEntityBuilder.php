<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\AccountDTO;
use App\DTO\CurrencyDTO;
use App\DTO\DTOInterface;
use App\DTO\MoneyDTO;
use App\Entity\Account;
use App\Entity\EntityInterface;
use Money\Currency;
use Money\Money;

final readonly class AccountEntityBuilder implements BuilderInterface
{
    /**
     * @param AccountDTO $dto
     * @param Account|null $entity
     * @return Account
     */
    public function buildFromDTO(DTOInterface $dto, ?EntityInterface $entity = null): Account
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

    /**
     * @param Account $entity
     * @return AccountDTO
     */
    public function buildDTO(EntityInterface $entity): AccountDTO
    {
        $dto = new AccountDTO();
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
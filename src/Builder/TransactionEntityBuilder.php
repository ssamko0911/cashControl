<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\CurrencyDTO;
use App\DTO\MoneyDTO;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use Money\Currency;
use Money\Money;

class TransactionEntityBuilder
{
    /**
     * @param TransactionDTO $dto
     * @param Account $account
     * @return Transaction
     */
    public function buildFromDTO(TransactionDTO $dto, Account $account): Transaction
    {
        $transaction = new Transaction();

        $transaction
            ->setAccount($account)
            ->setDescription($dto->description)
            ->setAmount(
                new Money(
                    $dto->amount->amount,
                    new Currency($dto->amount->currency->code)
                )
            )
            ->setType($dto->type);

        $this->updateAccount($account, $dto);

        return $transaction;
    }

    /**
     * @param Transaction $entity
     * @return TransactionDTO
     */
    public function buildDTO(Transaction $entity): TransactionDTO
    {
        $dto = new TransactionDTO();

        $currencyDto = new CurrencyDTO();
        $currencyDto->code = $entity->getAmount()->getCurrency()->getCode();

        $amountDTO = new MoneyDTO();
        $amountDTO->amount = $entity->getAmount()->getAmount();
        $amountDTO->currency = $currencyDto;

        $dto->amount = $amountDTO;
        $dto->description = $entity->getDescription();
        $dto->type = $entity->getType();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();

        //acc?
        //$dto->account = $entity->getAccount();

        return $dto;
    }

    private function updateAccount(Account $account, TransactionDTO $transactionDTO): void
    {

        $expenseAmount = new Money(
            $transactionDTO->amount->amount,
            new Currency($transactionDTO->amount->currency->code)
        );

        $accountTotal = $account->getTotal();

        $transactionDTO->type === TransactionType::TYPE_EXPENSE ?
            $account->setTotal($accountTotal->subtract($expenseAmount))
            : $account->setTotal($accountTotal->add($expenseAmount));
    }
}
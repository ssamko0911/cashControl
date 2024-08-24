<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\DTOInterface;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\EntityInterface;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use Money\Currency;
use Money\Money;

class TransactionEntityBuilder implements BuilderInterface
{
    /**
     * @param TransactionDTO $dto
     * @param Account $account
     * @return EntityInterface
     */
    public function buildFromDTO(DTOInterface $dto, Account $account): EntityInterface
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
     * @param Account $entity
     * @return DTOInterface
     */
    public function buildDTO(EntityInterface $entity): DTOInterface
    {
        // TODO: Implement buildDTO() method by myself;
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
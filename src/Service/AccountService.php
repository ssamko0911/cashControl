<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Enum\TransactionType;
use Money\Currency;
use Money\Money;

final readonly class AccountService
{
    public function update(Account $account, TransactionDTO $transactionDTO): void
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

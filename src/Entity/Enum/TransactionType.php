<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum TransactionType: string
{
    case TYPE_EXPENSE = 'expense';
    case TYPE_INCOME = 'income';
}

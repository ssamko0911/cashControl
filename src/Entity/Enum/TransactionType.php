<?php

namespace App\Entity\Enum;

enum TransactionType: string
{
    case TYPE_EXPENSE = 'expense';
    case TYPE_INCOME = 'income';
}

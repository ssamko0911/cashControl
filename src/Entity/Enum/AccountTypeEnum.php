<?php

namespace App\Entity\Enum;

enum AccountTypeEnum: string
{
    case TYPE_DEBIT = 'Debit';
    case TYPE_CREDIT = 'Credit';
}

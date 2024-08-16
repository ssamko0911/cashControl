<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum AccountTypeEnum: string
{
    case TYPE_DEBIT = 'Debit';
    case TYPE_CREDIT = 'Credit';
}

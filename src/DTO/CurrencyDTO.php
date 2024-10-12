<?php

declare(strict_types=1);

namespace App\DTO;

use App\Security\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;

#[Groups([
    AccessGroup::ACCOUNT_READ,
    AccessGroup::ACCOUNT_CREATE,
    AccessGroup::ACCOUNT_EDIT,
    AccessGroup::TRANSACTION_READ,
    AccessGroup::TRANSACTION_CREATE,
    AccessGroup::CATEGORY_READ,
    AccessGroup::CATEGORY_CREATE,
])]
final class CurrencyDTO
{
    public string $code;
}
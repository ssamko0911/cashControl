<?php

namespace App\DTO;

use App\Entity\Enum\AccountTypeEnum;
use App\Security\AccessGroup;
use DateTime;
use Money\Money;
use Symfony\Component\Serializer\Attribute\Groups;

final class AccountDTO
{
    #[Groups([
        AccessGroup::ACCOUNT_READ,
        AccessGroup::ACCOUNT_CREATE,
        AccessGroup::ACCOUNT_EDIT
    ])]
    public string $description;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
        AccessGroup::ACCOUNT_CREATE,
    ])]
    public Money $total;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
        AccessGroup::ACCOUNT_CREATE
    ])]
    public AccountTypeEnum $accountType;

    #[Groups([
        AccessGroup::ACCOUNT_READ
    ])]
    public int $id;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
    ])]
    public DateTime $createdAt;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
    ])]
    public DateTime $updatedAt;
}
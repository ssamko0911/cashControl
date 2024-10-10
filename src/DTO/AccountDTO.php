<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Enum\AccountTypeEnum;
use App\Security\AccessGroup;
use DateTime;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class AccountDTO implements DTOInterface
{
    #[Groups([
        AccessGroup::ACCOUNT_READ,
        AccessGroup::TRANSACTION_READ,
        AccessGroup::TRANSACTION_CREATE,
    ])]
    public int $id;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
        AccessGroup::ACCOUNT_CREATE,
        AccessGroup::ACCOUNT_EDIT,
        AccessGroup::TRANSACTION_READ,
    ])]
    public string $name;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
        AccessGroup::ACCOUNT_CREATE,
        AccessGroup::ACCOUNT_EDIT
    ])]
    #[Assert\NotBlank(
        groups: [
            AccessGroup::ACCOUNT_CREATE,
            AccessGroup::ACCOUNT_EDIT,
        ]
    )]
    public string $description;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
        AccessGroup::ACCOUNT_CREATE,
    ])]
    public MoneyDTO $total;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
        AccessGroup::ACCOUNT_CREATE
    ])]
    public AccountTypeEnum $accountType;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
    ])]
    public DateTime $createdAt;

    #[Groups([
        AccessGroup::ACCOUNT_READ,
    ])]
    public DateTime $updatedAt;
}
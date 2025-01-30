<?php

namespace App\DTO;

use App\Entity\Enum\TransactionType;
use App\Security\AccessGroup;
use DateTime;
use Symfony\Component\Serializer\Attribute\Groups;

class TransactionDTO implements DTOInterface
{
    #[Groups([
        AccessGroup::TRANSACTION_READ,
    ])]
    public int $id;

    #[Groups([
        AccessGroup::TRANSACTION_READ,
        AccessGroup::TRANSACTION_CREATE,
        AccessGroup::TRANSACTION_EDIT,
    ])]
    public MoneyDTO $amount;

    #[Groups([
        AccessGroup::TRANSACTION_READ,
        AccessGroup::TRANSACTION_CREATE,
        AccessGroup::TRANSACTION_EDIT,
    ])]
    public string $description;

    #[Groups([
        AccessGroup::TRANSACTION_READ,
    ])]
    public DateTime $createdAt;

    #[Groups([
        AccessGroup::TRANSACTION_READ,
    ])]
    public DateTime $updatedAt;

    #[Groups([
        AccessGroup::TRANSACTION_READ,
    ])]
    public AccountDTO $account;

    #[Groups([
        AccessGroup::TRANSACTION_READ,
        AccessGroup::TRANSACTION_CREATE,
        AccessGroup::TRANSACTION_EDIT,
    ])]
    public TransactionType $type;

    #[Groups([
        AccessGroup::TRANSACTION_READ,
        AccessGroup::TRANSACTION_CREATE,
    ])]
    public CategoryDTO $category;
}
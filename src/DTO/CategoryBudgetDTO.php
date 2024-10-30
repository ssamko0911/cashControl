<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\CategoryBudget;
use App\Entity\EntityInterface;
use App\Security\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

class CategoryBudgetDTO implements DTOInterface
{
    #[Groups([
        AccessGroup::CATEGORY_READ,
        AccessGroup::CATEGORY_EDIT,
        AccessGroup::CATEGORY_CREATE,
        AccessGroup::TRANSACTION_READ,
    ])]
    public ?MoneyDTO $budgetLimit;

    #[Groups([
        AccessGroup::CATEGORY_READ,
        AccessGroup::TRANSACTION_READ,
    ])]
    public string $monthYear;

    #[Groups([
        AccessGroup::CATEGORY_READ,
        AccessGroup::TRANSACTION_READ,
    ])]
    public MoneyDTO $currentSpending;

    #[Groups([
        AccessGroup::CATEGORY_READ,
        AccessGroup::TRANSACTION_READ,
    ])]
    public bool $overBudget;

    #[Ignore] public function getEntityObject(): EntityInterface
    {
        return new CategoryBudget();
    }
}

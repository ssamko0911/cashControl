<?php

declare(strict_types=1);

namespace App\DTO;

use App\Security\AccessGroup;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Groups;

class CategoryBudgetDTO
{
    #[Groups([
        AccessGroup::CATEGORY_READ,
        AccessGroup::CATEGORY_EDIT,
        AccessGroup::CATEGORY_CREATE,
    ])]
    public ?MoneyDTO $limit;

    #[Groups([
        AccessGroup::CATEGORY_READ,
    ])]
    public DateTimeImmutable $monthYear;

    #[Groups([
        AccessGroup::CATEGORY_READ,
    ])]
    public MoneyDTO $currentSpending;

    #[Groups([
        AccessGroup::CATEGORY_READ,
    ])]
    public bool $isOverBudget;
}

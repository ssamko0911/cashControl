<?php

declare(strict_types = 1);

namespace App\DTO;

use App\Security\AccessGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

#[Groups([
    AccessGroup::CATEGORY_CREATE,
    AccessGroup::CATEGORY_READ,
    AccessGroup::CATEGORY_EDIT,
])]
final class CategoryDTO
{
    #[Groups([
        AccessGroup::TRANSACTION_READ,
        AccessGroup::TRANSACTION_CREATE,
    ])]
    public int $id;

    public string $name;

    public string $description;

    public CategoryBudgetDTO $monthlyBudget;
}

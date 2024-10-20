<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;
use App\Entity\EntityInterface;
use App\Security\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[Groups([
    AccessGroup::CATEGORY_CREATE,
    AccessGroup::CATEGORY_READ,
    AccessGroup::CATEGORY_EDIT,
    AccessGroup::TRANSACTION_READ,
])]
final class CategoryDTO implements DTOInterface
{
    public int $id;

    public string $name;

    public string $description;

    public ?CategoryBudgetDTO $monthlyBudget;

    #[Ignore] public function getEntityObject(): EntityInterface
    {
        return new Category();
    }
}

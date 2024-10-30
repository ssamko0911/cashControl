<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;
use App\Entity\CategoryBudget;
use App\Entity\EntityInterface;
use App\Security\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Items;
use Nelmio\ApiDocBundle\Annotation\Model;

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

    #[Assert\All(new Assert\Type(type: CategoryBudget::class))]
    #[Property(
        type: 'array',
        items: new Items(ref: new Model(type: CategoryBudget::class)),
    )]
    /** @var CategoryBudgetDTO[] $expirations */
    public array $monthlyBudgets;

    #[Ignore] public function getEntityObject(): EntityInterface
    {
        return new Category();
    }
}

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

final class CategoryDTO implements DTOInterface
{

    #[Groups([
        AccessGroup::CATEGORY_READ,
        AccessGroup::CATEGORY_EDIT,
        AccessGroup::TRANSACTION_READ,
    ])]
    public int $id;


    #[Groups([
        AccessGroup::CATEGORY_CREATE,
        AccessGroup::CATEGORY_READ,
        AccessGroup::CATEGORY_EDIT,
        AccessGroup::TRANSACTION_READ,
    ])]
    public string $name;


    #[Groups([
        AccessGroup::CATEGORY_CREATE,
        AccessGroup::CATEGORY_READ,
        AccessGroup::CATEGORY_EDIT,
        AccessGroup::TRANSACTION_READ,
    ])]
    public string $description;


    // TODO: EDIT
    #[Groups([
        AccessGroup::CATEGORY_READ,
        AccessGroup::TRANSACTION_READ,
    ])]
    #[Assert\All(new Assert\Type(type: CategoryBudget::class))]
    #[Property(
        type: 'array',
        items: new Items(ref: new Model(type: CategoryBudget::class)),
    )]
    /** @var CategoryBudgetDTO[] $monthlyBudgets */
    public array $monthlyBudgets;

    #[Ignore] public function getEntityObject(): EntityInterface
    {
        return new Category();
    }
}

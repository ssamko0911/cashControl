<?php

declare(strict_types=1);

namespace App\Manager;

use App\Builder\CategoryEntityBuilder;
use App\DTO\CategoryDTO;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CategoryManager
{
    public function __construct(
        private EntityManagerInterface $manager,
        private CategoryEntityBuilder           $builder
    )
    {
    }

    public function create(CategoryDTO $categoryDTO): Category
    {
        $category = $this->builder->buildFromDTO($categoryDTO);

        $this->manager->persist($category);
        $this->manager->flush();

        return $category;
    }
}
<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryBudgetRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Money\Currency;
use Money\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryBudgetRepository::class)]
class CategoryBudget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: 'money')]
    private ?Money $limit = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $monthYear;

    #[ORM\Column(type: 'money')]
    private Money $currentSpending;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isOverBudget = false;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'monthlyBudgets')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false)]
    private Category $category;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): CategoryBudget
    {
        $this->id = $id;

        return $this;
    }

    public function getLimit(): ?Money
    {
        return $this->limit;
    }

    public function setLimit(?Money $limit): CategoryBudget
    {
        $this->limit = $limit;

        return $this;
    }

    public function getMonthYear(): DateTimeImmutable
    {
        return $this->monthYear;
    }

    public function setMonthYear(DateTimeImmutable $monthYear): CategoryBudget
    {
        $this->monthYear = $monthYear;

        return $this;
    }

    public function getCurrentSpending(): Money
    {
        return $this->currentSpending;
    }

    public function setCurrentSpending(Money $currentSpending): CategoryBudget
    {
        $this->currentSpending = $currentSpending;

        return $this;
    }

    public function isOverBudget(): bool
    {
        if ($this->currentSpending > $this->limit) {
            $this->setIsOverBudget(true);
        } else {
            $this->setIsOverBudget(false);
        }

        return $this->isOverBudget;
    }

    public function setIsOverBudget(bool $isOverBudget): CategoryBudget
    {
        $this->isOverBudget = $isOverBudget;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): CategoryBudget
    {
        $this->category = $category;

        return $this;
    }
}

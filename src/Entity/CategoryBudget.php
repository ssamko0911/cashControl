<?php

declare(strict_types=1);

namespace App\Entity;

use App\DTO\CategoryBudgetDTO;
use App\DTO\DTOInterface;
use App\Repository\CategoryBudgetRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Money\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryBudgetRepository::class)]
class CategoryBudget implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: 'money', nullable: true)]
    private ?Money $budgetLimit = null;

    #[ORM\Column(type: Types::STRING)]
    private string $monthYear;

    #[ORM\Column(type: 'money')]
    private Money $currentSpending;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isOverBudget = false;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'monthlyBudgets')]
    private Category $category;

    public function __construct()
    {
        $this->monthYear = (new DateTimeImmutable())->format('F Y');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): CategoryBudget
    {
        $this->id = $id;

        return $this;
    }

    public function getBudgetLimit(): ?Money
    {
        return $this->budgetLimit;
    }

    public function setBudgetLimit(?Money $budgetLimit): CategoryBudget
    {
        $this->budgetLimit = $budgetLimit;

        return $this;
    }

    public function getMonthYear(): string
    {
        return $this->monthYear;
    }

    public function setMonthYear(string $monthYear): CategoryBudget
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
        if ($this->currentSpending > $this->budgetLimit) {
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

    public function getDTO(): DTOInterface
    {
        return new CategoryBudgetDTO();
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

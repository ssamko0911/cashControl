<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    private string $description;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: CategoryBudget::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $monthlyBudgets;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->monthlyBudgets = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Category
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Category
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Category
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): Category
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setCategory($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): Category
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
        }

        return $this;
    }

    /**
     * @return Collection<int, CategoryBudget>
     */
    public function getMonthlyBudgets(): Collection
    {
        return $this->monthlyBudgets;
    }

    public function addMonthlyBudget(CategoryBudget $budget): Category
    {
        if (!$this->monthlyBudgets->contains($budget)) {
            $this->monthlyBudgets->add($budget);
        }

        return $this;
    }

    public function removeMonthlyBudget(CategoryBudget $budget): Category
    {
        if ($this->monthlyBudgets->contains($budget)) {
            $this->monthlyBudgets->removeElement($budget);
        }

        return $this;
    }
}
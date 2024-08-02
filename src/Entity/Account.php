<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\AccountTypeEnum;
use App\Repository\AccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(enumType: AccountTypeEnum::class)]
    private AccountTypeEnum $accountType;

    #[ORM\Column(type: Types::STRING)]
    private string $description;

    #[ORM\Column(type: 'money')]
    private Money $total;

    public function getAccountType(): AccountTypeEnum
    {
        return $this->accountType;
    }

    public function setAccountType(AccountTypeEnum $accountType): Account
    {
        $this->accountType = $accountType;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Account
    {
        $this->description = $description;
        return $this;
    }

    public function getTotal(): Money
    {
        return $this->total;
    }

    public function setTotal(Money $total): Account
    {
        $this->total = $total;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Account
    {
        $this->id = $id;
        return $this;
    }
}


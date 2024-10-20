<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\CurrencyDTO;
use App\DTO\MoneyDTO;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Category;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use Money\Currency;
use Money\Money;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class TransactionEntityBuilder
{
    public function __construct(
        private AccountEntityBuilder  $accountEntityBuilder,
        private CategoryRepository    $categoryRepository,
        private TransactionRepository $transactionRepository
    )
    {
    }

    /**
     * @param TransactionDTO $dto
     * @param int $categoryId
     * @param Account $account
     * @return Transaction
     */
    public function buildFromDTO(TransactionDTO $dto, int $categoryId, Account $account): Transaction
    {
        $transaction = new Transaction();

        /** @var Category|null $category */
        $category = $this->categoryRepository->find($categoryId);

        if (null === $category) {
            throw new NotFoundHttpException('Category not found');
        }

        $transaction
            ->setAccount($account)
            ->setDescription($dto->description)
            ->setAmount(
                new Money(
                    $dto->amount->amount,
                    new Currency($dto->amount->currency->code)
                )
            )
            ->setCategory($category)
            ->setType($dto->type);

        $this->updateAccount($account, $dto);
        // TODO: updateMonthlyBudget;

        return $transaction;
    }

    /**
     * @param Transaction $entity
     * @return TransactionDTO
     */
    public function buildDTO(Transaction $entity): TransactionDTO
    {
        $dto = new TransactionDTO();

        $currencyDto = new CurrencyDTO();
        $currencyDto->code = $entity->getAmount()->getCurrency()->getCode();

        $amountDTO = new MoneyDTO();
        $amountDTO->amount = $entity->getAmount()->getAmount();
        $amountDTO->currency = $currencyDto;

        $dto->id = $entity->getId();
        $dto->amount = $amountDTO;
        $dto->description = $entity->getDescription();
        $dto->type = $entity->getType();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();

        $dto->account = $this->accountEntityBuilder->buildDTO($entity->getAccount());

        return $dto;
    }

    /**
     * @param Transaction[] $transactions
     * @return TransactionDTO[]
     */
    public function buildDTOs(array $transactions): array
    {
        $transactionDtos = [];
        foreach ($transactions as $transaction) {
            $transactionDtos[] = $this->buildDTO($transaction);
        }

        return $transactionDtos;
    }

    private function updateAccount(Account $account, TransactionDTO $transactionDTO): void
    {

        $expenseAmount = new Money(
            $transactionDTO->amount->amount,
            new Currency($transactionDTO->amount->currency->code)
        );

        $accountTotal = $account->getTotal();

        $transactionDTO->type === TransactionType::TYPE_EXPENSE ?
            $account->setTotal($accountTotal->subtract($expenseAmount))
            : $account->setTotal($accountTotal->add($expenseAmount));
    }

    private function updateCategoryBudget(TransactionDTO $transactionDTO, Category $category)
    {
        /** @var Transaction|null $previousTransaction */
        $previousTransaction = $this->transactionRepository->find($transactionDTO->id - 1); // investigate the behaviour on -1 or NULL

        // if we have the same mo => there's no need in updating category budget;
        // if we have next mo => create category budget (limit => ? null OR from the previous mo);
    }

    private function differsByOneMonth(DateTimeImmutable $date1, DateTimeImmutable $date2): bool
    {
        $interval = $date1->diff($date2);
        $totalMonths = ($interval->y * 12) + $interval->m;
        return abs($totalMonths) === 1 && $interval->d === 0;
    }
}
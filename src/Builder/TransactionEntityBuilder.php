<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\CurrencyDTO;
use App\DTO\MoneyDTO;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Category;
use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use App\Service\CurrencyExchangeService;
use Money\Currency;
use Money\Money;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class TransactionEntityBuilder
{
    public function __construct(
        private AccountEntityBuilder    $accountEntityBuilder,
        private CategoryRepository      $categoryRepository,
        private CurrencyExchangeService $currencyExchangeService
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

        $money = new Money(
            $dto->amount->amount,
            new Currency($dto->amount->currency->code)
        );

        $newMoney = $this->currencyExchangeService->exchange($money);

        $transaction
            ->setAccount($account)
            ->setDescription($dto->description)
            ->setAmount($newMoney)
            ->setCategory($category)
            ->setType($dto->type);

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
}

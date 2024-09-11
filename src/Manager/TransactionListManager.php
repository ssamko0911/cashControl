<?php

namespace App\Manager;

use App\Builder\TransactionEntityBuilder;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;

final readonly class TransactionListManager
{
    public function __construct(
        private TransactionEntityBuilder $builder,
        private PaginationService $paginationService
    ) {
    }

    /**
     * @param Request $request
     * @param Account $account
     * @return array<string, array> //TODO: Check this on as well;
     */
    public function getList(Request $request, Account $account): array
    {
        $transactions = $this->paginationService->paginate(
            $account,
            $request->query->getInt('page', 1)
        );

        $transactionDtos = [];
        foreach ($transactions['data'] as $transaction) {
            $transactionDtos[] = $this->builder->buildDTO($transaction);
        }

        return [
            'data' => $transactionDtos,
            'meta' => $transactions['meta'],
        ];
    }
}
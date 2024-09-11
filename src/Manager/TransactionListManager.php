<?php

namespace App\Manager;

use App\Builder\TransactionEntityBuilder;
use App\Entity\Account;
use App\Repository\TransactionRepository;
use App\Service\PaginationService;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

final readonly class TransactionListManager
{
    public function __construct(
        private TransactionEntityBuilder $builder,
        private PaginationService $paginationService,
        private TransactionRepository $transactionRepository
    ) {
    }

    /**
     * @param Request $request
     * @param Account $account
     * @return array<string, array> //TODO: Check this on as well;
     */
    public function getList(Request $request, Account $account): array
    {
        //TODO: split into smaller chunks;
        $qb = $this->transactionRepository->createQueryBuilder('t');

        $this->joinAccount($qb);

        $qb
            ->where('t.account = :account')
            ->setParameter('account', $account)
            ->groupBy('t')
            ->orderBy('t.id')
            ->getQuery();

        $this->applyDateRangeFilter($qb, $request);

        $transactions = $this->paginationService->paginate(
            $account,
            $request->query->getInt('page', 1),
            $qb
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

    private function applyDateRangeFilter(QueryBuilder $qb, Request $request): void
    {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        if (null !== $startDate) {
            $qb
                ->andWhere('t.createdAt >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if (null !== $endDate) {
            $qb
                ->andWhere('t.createdAt <= :endDate')
                ->setParameter('endDate', $endDate);
        }
    }

    private function joinAccount(QueryBuilder $qb): void
    {
        if (!in_array('acc', $qb->getAllAliases(), true)) {
            $qb->join(Account::class, 'acc');
        }
    }
}
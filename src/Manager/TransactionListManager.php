<?php

namespace App\Manager;

use App\Builder\TransactionEntityBuilder;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Service\PaginationService;
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
     * @return array{data: mixed, meta: array{totalPages: float, totalItems: int<0, max>}}.
     */
    public function getList(Request $request, Account $account): array
    {
        $qb = $this->getQueryBuilder();

        $this->buildQuery($qb, $account);

        $this->applyDateRangeFilter($qb, $request);

        $this->applySorting($qb, $request);

        $this->applySearchFilter($qb, $request);

        //TODO: filtering by ... whatever I want;

        $transactions = $this->paginationService->paginate(
            $request->query->getInt('page', 1),
            $qb
        );

        $transactionDtos = $this->builder->buildDTOs($transactions['data']);

        //TODO: consider the order;
        if ($request->get('sort') === 'amount') {
            $this->sortByAmount($transactionDtos);
        }

        return [
            'data' => $transactionDtos,
            'meta' => $transactions['meta'],
        ];
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->transactionRepository->createQueryBuilder('t');
    }

    private function buildQuery(QueryBuilder $qb, Account $entity): void
    {
        $this->joinAccount($qb);

        $qb
            ->where('t.account = :account')
            ->setParameter('account', $entity)
            ->groupBy('t')
            ->orderBy('t.id')
            ->getQuery();
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

    private function applySorting(QueryBuilder $qb, Request $request): void
    {
        $sortField = $request->get('sort');
        $sortOrder = strtoupper($request->get('order', 'ASC'));

        $validSortFields = [
            'id',
            'description',
            'amount',
            'createdAt',
            'type',
        ];

        $validSortDirections = [
            'ASC',
            'DESC',
        ];

        if (!in_array($sortOrder, $validSortDirections, true)) {
            $sortOrder = 'ASC';
        }

        if ($sortField === 'amount') {
            return;
        } elseif (in_array($sortField, $validSortFields, true)) {
            $qb->orderBy('t.'.$sortField, $sortOrder);
        } else {
            $qb->orderBy('t.id', 'ASC');
        }
    }

    /**
     * @param TransactionDTO[] $transactionDTOs
     * @return void
     */
    private function sortByAmount(array &$transactionDTOs): void
    {
        usort($transactionDTOs, function (TransactionDTO $a, TransactionDTO $b) {
            return intval($a->amount->amount) - intval($b->amount->amount);
        });
    }

    private function applySearchFilter(QueryBuilder $qb, Request $request): void
    {
        $searchTerm = $request->get('search');

        if (null !== $searchTerm) {
            $qb->andWhere('t.description LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    }
}
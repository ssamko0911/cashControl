<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

final readonly class PaginationService
{
    private const int ITEMS_PER_PAGE = 3;

    public function __construct(
        private TransactionRepository $transactionRepository
    ) {
    }

    /**
     * @param int $page
     * @param QueryBuilder $qb
     * @return array{data: mixed, meta: array{totalPages: float, totalItems: int<0, max>}}
     */
    public function paginate(int $page, QueryBuilder $qb): array
    {
        $paginator = new Paginator($qb);

        $paginator->getQuery()->setFirstResult(self::ITEMS_PER_PAGE * ($page - 1))->setMaxResults(self::ITEMS_PER_PAGE);

        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / self::ITEMS_PER_PAGE);

        return [
            'data' => $paginator->getQuery()->getResult(),
            'meta' => [
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
            ],
        ];
    }
}
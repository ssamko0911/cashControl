<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
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
     * @param Account $account
     * @param int $page
     * @return array<string, array> // TODO: check this doc;
     */
    public function paginate(Account $account, int $page, QueryBuilder $qb): array
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
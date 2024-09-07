<?php

namespace App\Manager;

use App\Builder\TransactionEntityBuilder;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

final readonly class TransactionListManager
{
    private const int ITEMS_PER_PAGE = 3;

    public function __construct(
        private TransactionRepository $repository,
        private TransactionEntityBuilder $builder
    )
    {
    }

    public function getList(Request $request, Account $account): array
    {
        //TODO: refactor (move to custom Repo class)
        $page = $request->query->getInt('page', 1);

        //TODO: make a right join to get a set of account related transactions
        $query = $this->repository->createQueryBuilder('t')
            ->join(Account::class, 'account')
            ->where('t.account = :account')
            ->setParameter('account', $account)
            ->groupBy('t')
            ->orderBy('t.id')
            ->getQuery();

        //var_dump($query->getSQL());

        $paginator = new Paginator($query);

        $paginator->getQuery()->setFirstResult(self::ITEMS_PER_PAGE * ($page - 1))->setMaxResults(self::ITEMS_PER_PAGE);

        $totalItems = count($paginator);

        $totalPages = ceil($totalItems / self::ITEMS_PER_PAGE);

        $result = $paginator->getQuery()->getResult();

        //$this->repository->doSmth;

        $dtos = [];
        foreach ($result as $item) {
            $dtos[] = $this->builder->buildDTO($item);
        }

        //TODO: return only DTOs, other dataset is created in the Controller Class;
        return [
            'data' => $dtos,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems
        ];
    }
}
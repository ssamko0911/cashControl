<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
final class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function getTransactionsByAccountQuery(Account $account): Query
    {
        return $this->createQueryBuilder('t')
            ->join(Account::class, 'account')
            ->where('t.account = :account')
            ->setParameter('account', $account)
            ->groupBy('t')
            ->orderBy('t.id')
            ->getQuery();
    }
}
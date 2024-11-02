<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CategoryBudget;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;


class CategoryBudgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryBudget::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getByDate(DateTime $date): CategoryBudget|null
    {
        $dateAsString = $date->format('F Y');
        $qb = $this->createQueryBuilder('cb');

        return $qb
            ->where($qb
                ->expr()
                ->eq('cb.monthYear', ':date'))
            ->setParameter('date', $dateAsString)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

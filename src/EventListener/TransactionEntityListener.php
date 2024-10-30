<?php

namespace App\EventListener;

use App\Entity\Transaction;
use App\Repository\CategoryBudgetRepository;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\NonUniqueResultException;

#[AsEntityListener(event: Events::prePersist, entity: Transaction::class)]
final readonly class TransactionEntityListener
{
    public function __construct(
        private CategoryBudgetRepository $categoryBudgetRepository,
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function prePersist(Transaction $transaction): void
    {
        $currentDate = new DateTimeImmutable();
        $currentBudget = $this->categoryBudgetRepository->getByDate($currentDate);

        if ($currentBudget->getCurrentSpending()->greaterThan($currentBudget->getBudgetLimit())) {
            $currentBudget->setOverBudget(true);
        } else {
            $currentBudget->setOverBudget(false);
        }

        //re-do // youtube - component Symf messenger
        $this->em->flush();
    }
}

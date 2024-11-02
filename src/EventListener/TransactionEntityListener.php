<?php

namespace App\EventListener;

use App\Entity\CategoryBudget;
use App\Entity\Transaction;
use App\Repository\CategoryBudgetRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\NonUniqueResultException;

#[AsEntityListener(event: Events::prePersist, entity: Transaction::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Transaction::class)]
#[AsEntityListener(event: Events::preRemove, entity: Transaction::class)]
final readonly class TransactionEntityListener
{
    public function __construct(
        private CategoryBudgetRepository $categoryBudgetRepository,
        private EntityManagerInterface   $em
    )
    {
    }

    public function prePersist(Transaction $transaction, PrePersistEventArgs $args): void
    {
        $this->updateOverBudget($args);
    }

    public function preUpdate(PrePersistEventArgs $args): void
    {
        $this->updateOverBudget($args);
    }

    public function preRemove(PrePersistEventArgs $args): void
    {
        $this->updateOverBudget($args);
    }

    private function updateOverBudget(PrePersistEventArgs $args): void
    {
        /** @var Transaction $transaction */
        $transaction = $args->getObject();

        $date = $transaction->getCreatedAt();

        try {
            /** @var CategoryBudget|null $budget */
            $budget = $this->categoryBudgetRepository->getByDate($date);
        } catch (NonUniqueResultException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        if (null === $budget) {
            return;
        }

        if ($budget->getCurrentSpending()->greaterThan($budget->getBudgetLimit())) {
            $budget->setOverBudget(true);
        } else {
            $budget->setOverBudget(false);
        }

        $this->em->flush();
    }
}

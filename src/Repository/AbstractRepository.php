<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public static string $entityName;
    protected ManagerRegistry $managerRegistry;

    public function getEntityName(): string
    {
        return static::$entityName;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return parent::getEntityManager();
    }

    public function getRepositoryObject(?string $entityName = null): EntityRepository
    {
        return $this->getEntityManager()->getRepository($entityName);
    }

    public function save(EntityInterface $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(EntityInterface $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}

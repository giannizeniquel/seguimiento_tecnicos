<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function findByUser(string $userId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.assignedTo = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPending(): array
    {
        return $this->findByStatus(Activity::STATUS_PENDING);
    }

    public function findInProgress(): array
    {
        return $this->findByStatus(Activity::STATUS_IN_PROGRESS);
    }

    public function findCompleted(): array
    {
        return $this->findByStatus(Activity::STATUS_COMPLETED);
    }
}

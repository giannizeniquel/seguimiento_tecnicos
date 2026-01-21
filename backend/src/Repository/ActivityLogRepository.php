<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ActivityLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivityLog>
 */
class ActivityLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityLog::class);
    }

    public function findByActivity(string $activityId): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.activity = :activityId')
            ->setParameter('activityId', $activityId)
            ->orderBy('l.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUser(string $userId): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('l.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByAction(string $action): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.action = :action')
            ->setParameter('action', $action)
            ->orderBy('l.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

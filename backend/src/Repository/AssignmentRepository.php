<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Assignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assignment>
 */
class AssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    public function findByTechnician(string $technicianId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.technician = :technicianId')
            ->setParameter('technicianId', $technicianId)
            ->orderBy('a.assignedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByActivity(string $activityId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.activity = :activityId')
            ->setParameter('activityId', $activityId)
            ->getQuery()
            ->getResult();
    }
}

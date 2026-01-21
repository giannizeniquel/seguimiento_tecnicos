<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findByUser(string $userId, bool $unreadOnly = false): array
    {
        $qb = $this->createQueryBuilder('n')
            ->where('n.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('n.createdAt', 'DESC');

        if ($unreadOnly) {
            $qb->andWhere('n.isRead = false');
        }

        return $qb->getQuery()->getResult();
    }

    public function markAsRead(string $notificationId): void
    {
        $this->createQueryBuilder('n')
            ->update(Notification::class, 'n')
            ->set('n.isRead', 'true')
            ->where('n.id = :id')
            ->setParameter('id', $notificationId)
            ->getQuery()
            ->execute();
    }

    public function markAllAsRead(string $userId): void
    {
        $this->createQueryBuilder('n')
            ->update(Notification::class, 'n')
            ->set('n.isRead', 'true')
            ->where('n.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }
}

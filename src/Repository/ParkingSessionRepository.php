<?php

namespace App\Repository;

use App\Entity\ParkingSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParkingSession>
 */
class ParkingSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParkingSession::class);
    }

    public function findActiveSessionByLicensePlate(string $licencePlate): ?ParkingSession {
        $qb = $this->createQueryBuilder('ps')
            ->where('ps.licencePlate = :licencePlate')
            ->andWhere('ps.status = :status')
            ->andWhere('ps.actualExit IS NULL')
            ->setParameter('licencePlate', $licencePlate)
            ->setParameter('status', 'ACTIVE');

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}

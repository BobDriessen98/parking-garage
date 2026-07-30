<?php

namespace App\Repository;

use App\Entity\ParkingGarage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParkingGarage>
 *
 * @method ParkingGarage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParkingGarage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParkingGarage[]    findAll()
 * @method ParkingGarage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParkingGarageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParkingGarage::class);
    }
}
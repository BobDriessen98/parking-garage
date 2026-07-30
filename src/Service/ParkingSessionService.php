<?php

namespace App\Service;

use App\Entity\ParkingSession;
use App\Entity\ParkingSession\DTO\Input\ParkingSessionEndInputDTO;
use App\Entity\ParkingSession\DTO\Input\ParkingSessionStartInputDTO;
use App\Repository\ParkingGarageRepository;
use App\Repository\ParkingSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Lock\LockFactory;

class ParkingSessionService {
    public function __construct(
        private EntityManagerInterface $entityManager, 
        private ParkingSessionRepository $parkingSessionRepository,
        private ParkingGarageRepository $parkingGarageRepository,
        private LockFactory $lockFactory
        ) {}

    public function startParkingSession(ParkingSessionStartInputDTO $dto): ?ParkingSession {
        $parkingGarage = $this->parkingGarageRepository->find($dto->garageId);

        if(!$parkingGarage){
            return null;
        }

        $parkingSession = ParkingSession::fromDTO($dto, $parkingGarage);

        $lock = $this->lockFactory->createLock('parking-garage-' . $parkingGarage->getId());

        if (!$lock->acquire(blocking: true)) {
            return null;
        }

        try {
            $activeCount = $this->parkingSessionRepository->count(['status' => 'ACTIVE', 'garage' => $parkingGarage]);
        
            if($activeCount >= $parkingGarage->getCapacity()) {
                return null;
            }

            $this->entityManager->persist($parkingSession);
            $this->entityManager->flush(); 

            return $parkingSession;
        } finally {
            $lock->release();
        }
    } 

    public function endParkingSession(ParkingSessionEndInputDTO $dto): ParkingSession {
        $parkingSession = $this->parkingSessionRepository->findActiveSessionByLicensePlate($dto->licencePlate);

        if(!$parkingSession) {
            throw new EntityNotFoundException('No active parking session found for: ' . $dto->licencePlate);
        }

        $parkingSession->setActualExit($dto->actualExit);
        $parkingSession->setStatus('ENDED');

        $this->entityManager->flush(); 

        return $parkingSession;
    }
}
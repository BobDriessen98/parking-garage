<?php

namespace App\State\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\ParkingSession\DTO\Output\ParkingSessionDTO;
use App\Repository\ParkingSessionRepository;
use Override;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ActiveParkingSessionProvider implements ProviderInterface {
    public function __construct(private ParkingSessionRepository $parkingSessionRepository){}

    #[Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ParkingSessionDTO
    {
        $licencePlate = $uriVariables['licencePlate'] ?? null;

        if(!$licencePlate){
            throw new BadRequestHttpException('LicencePlate param ontbreekt');
        }

        $parkingSession = $this->parkingSessionRepository->findActiveSessionByLicensePlate($licencePlate);

        if(!$parkingSession){
            throw new NotFoundHttpException('Geen actieve parking session gevonden voor: ' . $licencePlate);
        }

        return new ParkingSessionDTO($parkingSession->getLicencePlate(), $parkingSession->getActualEntry(), $parkingSession->getActualExit());
    }
}
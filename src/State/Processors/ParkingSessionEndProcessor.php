<?php

namespace App\State\Processors;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ParkingSession\DTO\Input\ParkingSessionEndInputDTO;
use App\Entity\ParkingSession\DTO\Output\ParkingSessionDTO;
use App\Message\SendEmailMessage;
use App\Repository\ParkingSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<ParkingSessionEndInputDTO, ParkingSessionDTO>
 */
final readonly class ParkingSessionEndProcessor implements ProcessorInterface {
    private const PARK_ACTION = 'left';

    public function __construct(
        private ParkingSessionRepository $parkingSessionRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBusInterface
        ){}

    #[Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ParkingSessionDTO
    {
        $parkingSession = $this->parkingSessionRepository->findActiveSessionByLicensePlate($data->licencePlate);

        if(!$parkingSession) {
            throw new NotFoundHttpException('Geen sessie gevonden voor: ' . $data->licencePlate);
        }

        $parkingSession->setActualExit($data->actualExit);
        $parkingSession->setStatus('ENDED');

        $this->entityManager->flush(); 

        $parkingSessionDto = new ParkingSessionDTO(
            $parkingSession->getLicencePlate(), 
            $parkingSession->getActualEntry(), 
            $parkingSession->getActualExit());

        $this->messageBusInterface->dispatch(new SendEmailMessage($parkingSession->getLicencePlate(), self::PARK_ACTION));

        return $parkingSessionDto;
    }
}
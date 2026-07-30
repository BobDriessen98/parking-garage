<?php

namespace App\State\Processors;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ParkingSession;
use App\Entity\ParkingSession\DTO\Output\ParkingSessionDTO;
use App\Entity\ParkingSession\DTO\Input\ParkingSessionStartInputDTO;
use App\Message\SendEmailMessage;
use App\Repository\ParkingGarageRepository;
use App\Repository\ParkingSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<ParkingSessionStartInputDTO, ParkingSessionDTO>
 */
final readonly class ParkingSessionStartProcessor implements ProcessorInterface {
    private const PARK_ACTION = 'entered';

    public function __construct(
        private ParkingSessionRepository $parkingSessionRepository,
        private ParkingGarageRepository $parkingGarageRepository,
        private LockFactory $lockFactory,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBusInterface
        ){}

    #[Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ParkingSessionDTO
    {
        $parkingGarage = $this->parkingGarageRepository->find($data->garageId);

        if(!$parkingGarage){
            throw new NotFoundHttpException('Er bestaat geen garage met dit ID.');
        }

        $parkingSession = ParkingSession::fromDTO($data, $parkingGarage);

        if($this->parkingSessionRepository->findActiveSessionByLicensePlate($parkingSession->getLicencePlate())){
            throw new UnprocessableEntityHttpException('Er is al een sessie gaande voor dit kenteken.');
        };

        $lock = $this->lockFactory->createLock('parking-garage-' . $parkingGarage->getId());

        if (!$lock->acquire(blocking: true)) {
            throw new ServiceUnavailableHttpException('Probeer het later opnieuw');
        }

        try {
            $activeCount = $this->parkingSessionRepository->count(['status' => 'ACTIVE', 'garage' => $parkingGarage]);
        
            if($activeCount >= $parkingGarage->getCapacity()) {
                throw new UnprocessableEntityHttpException('Garage is vol');
            }

            $this->entityManager->persist($parkingSession);
            $this->entityManager->flush(); 

            $parkingSessionDTO = new ParkingSessionDTO(
                $parkingSession->getLicencePlate(),
                $parkingSession->getActualEntry(),
                $parkingSession->getActualExit()
            );

            $this->messageBusInterface->dispatch(new SendEmailMessage($parkingSession->getLicencePlate(), self::PARK_ACTION));

            return $parkingSessionDTO;
        } finally {
            $lock->release();
        }
    }
}
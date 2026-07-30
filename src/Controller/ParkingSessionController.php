<?php

namespace App\Controller;

use App\Entity\ParkingSession\DTO\Output\ParkingSessionDTO;
use App\Entity\ParkingSession\DTO\Input\ParkingSessionStartInputDTO;
use App\Entity\ParkingSession\DTO\Input\ParkingSessionEndInputDTO;
use App\Service\ParkingSessionService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class ParkingSessionController extends AbstractController
{
    #[Route('/api/parking/start-session', name: 'app_parking_start_session', methods: ['POST'])]
    public function startSession(
        #[MapRequestPayload] ParkingSessionStartInputDTO $parkingSessionStartInputDTO, 
        ParkingSessionService $parkingSessionService
    ): JsonResponse {
        $parkingSession = $parkingSessionService->startParkingSession($parkingSessionStartInputDTO);

        if (!$parkingSession) {
            return $this->json([
                'success' => false,
                'message' => 'Parkeersessie kon niet gestart worden (garage is vol of bestaat niet).'
            ], Response::HTTP_CONFLICT);
        }

        return $this->json([
            'success' => true,
            'message' => 'Parkeersessie succesvol gestart.',
            'data' => [
                'garageId' => $parkingSession->getGarage()->getId(),
                'licencePlate' => $parkingSession->getLicencePlate(),
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/parking/end-session', name: 'app_parking_end_session', methods: ['POST'])]
    public function endSession(
        #[MapRequestPayload] ParkingSessionEndInputDTO $dto, 
        ParkingSessionService $parkingSessionService
    ): JsonResponse {
        try {
            $parkingSession = $parkingSessionService->endParkingSession($dto);
            $parkingEndedDto = new ParkingSessionDTO($parkingSession->getLicencePlate(), $parkingSession->getActualEntry(), $parkingSession->getActualExit());

            return $this->json($parkingEndedDto, Response::HTTP_OK);
        } catch(EntityNotFoundException $e) {
            return $this->json($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
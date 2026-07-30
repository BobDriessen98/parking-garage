<?php

namespace App\Controller;

use App\Entity\ParkingGarage;
use App\Repository\ParkingGarageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class ParkingGarageController extends AbstractController
{
    // GET /api/parking/garage -> Lijst van alle garages
    #[Route('/api/parking/garage', name: 'app_parking_garage_index', methods: ['GET'])]
    public function index(ParkingGarageRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $garages = $repository->findAll();
        $json = $serializer->serialize($garages, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    // POST /api/parking/garage -> Maak een nieuwe garage aan
    #[Route('/api/parking/garage', name: 'app_parking_garage_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        /** @var ParkingGarage $parkingGarage */
        $parkingGarage = $serializer->deserialize(
            $request->getContent(), 
            ParkingGarage::class, 
            'json'
        );

        if (!$parkingGarage->getCapacity()) {
            return new JsonResponse(['error' => 'capacity is verplicht'], Response::HTTP_BAD_REQUEST);
        }

        $maxReservations = (int) ($parkingGarage->getCapacity() * 0.25);
        $parkingGarage->setMaxReservationCapacity($maxReservations);

        $entityManager->persist($parkingGarage);
        $entityManager->flush();

        $json = $serializer->serialize($parkingGarage, 'json');
        
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    // GET /api/parking/garage/{id} -> Bekijk 1 garage
    #[Route('/api/parking/garage/{id}', name: 'app_parking_garage_show', methods: ['GET'])]
    public function show(ParkingGarage $parkingGarage, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($parkingGarage, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    // PUT /api/parking/garage/{id} -> Update 1 garage
    #[Route('/api/parking/garage/{id}', name: 'app_parking_garage_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, ParkingGarage $parkingGarage, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $serializer->deserialize(
            $request->getContent(),
            ParkingGarage::class,
            'json',
            [
                'object_to_populate' => $parkingGarage
            ]
        );

        $maxReservations = (int) ($parkingGarage->getCapacity() * 0.25);
        $parkingGarage->setMaxReservationCapacity($maxReservations);

        $entityManager->flush();

        $json = $serializer->serialize($parkingGarage, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    // DELETE /api/parking/garage/{id} -> Verwijder 1 garage
    #[Route('/api/parking/garage/{id}', name: 'app_parking_garage_delete', methods: ['DELETE'])]
    public function delete(ParkingGarage $parkingGarage, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($parkingGarage);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Garage succesvol verwijderd'], Response::HTTP_OK);
    }
}
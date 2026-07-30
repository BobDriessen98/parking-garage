<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Entity\ParkingGarage;
use App\Entity\ParkingSession\DTO\Input\ParkingSessionEndInputDTO;
use App\Entity\ParkingSession\DTO\Input\ParkingSessionStartInputDTO;
use App\Entity\ParkingSession\DTO\Output\ParkingSessionDTO;
use App\Repository\ParkingSessionRepository;
use App\State\Processors\ParkingSessionEndProcessor;
use App\State\Processors\ParkingSessionStartProcessor;
use App\State\Providers\ActiveParkingSessionProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'ParkingSession',
    operations: [
        new Get(
            uriTemplate: '/get-active-session/{garageId}/{licencePlate}',
            uriVariables: [
                'licencePlate' => new Link(fromClass: ParkingSession::class, identifiers: ['licencePlate']),
            ],
            provider: ActiveParkingSessionProvider::class
        ),
        new Post(
            uriTemplate: '/start-parking-session',
            input: ParkingSessionStartInputDTO::class,
            output: ParkingSessionDTO::class,
            processor: ParkingSessionStartProcessor::class
        ),
        new Post(
            uriTemplate: '/end-parking-session',
            input: ParkingSessionEndInputDTO::class,
            output: ParkingSessionDTO::class,
            processor: ParkingSessionEndProcessor::class
        )
    ]
)]
#[ORM\Entity(repositoryClass: ParkingSessionRepository::class)]
class ParkingSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reservationCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $plannedStart = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $plannendEnd = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $actualEntry = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $actualExit = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(length: 50, nullable: false)]
    #[Assert\NotBlank(message: 'Licence plate is required')]
    private ?string $licencePlate;

    #[ORM\ManyToOne(inversedBy: 'parkingSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ParkingGarage $garage = null;

    public static function fromDTO(ParkingSessionStartInputDTO $dto, ParkingGarage $garage): self {
        $session = new self();
        
        $session->setGarage($garage);
        $session->setActualEntry($dto->actualEntry);
        $session->setLicencePlate($dto->licencePlate);
        $session->setStatus('ACTIVE');

        return $session;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservationCode(): ?string
    {
        return $this->reservationCode;
    }

    public function setReservationCode(?string $reservationCode): static
    {
        $this->reservationCode = $reservationCode;

        return $this;
    }

    public function getPlannedStart(): ?\DateTimeImmutable
    {
        return $this->plannedStart;
    }

    public function setPlannedStart(?\DateTimeImmutable $plannedStart): static
    {
        $this->plannedStart = $plannedStart;

        return $this;
    }

    public function getPlannendEnd(): ?\DateTimeImmutable
    {
        return $this->plannendEnd;
    }

    public function setPlannendEnd(?\DateTimeImmutable $plannendEnd): static
    {
        $this->plannendEnd = $plannendEnd;

        return $this;
    }

    public function getActualEntry(): ?\DateTimeImmutable
    {
        return $this->actualEntry;
    }

    public function setActualEntry(?\DateTimeImmutable $actualEntry): static
    {
        $this->actualEntry = $actualEntry;

        return $this;
    }

    public function getActualExit(): ?\DateTimeImmutable
    {
        return $this->actualExit;
    }

    public function setActualExit(?\DateTimeImmutable $actualExit): static
    {
        $this->actualExit = $actualExit;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLicencePlate(): ?string
    {
        return $this->licencePlate;
    }

    public function setLicencePlate(string $LicencePlate): static
    {
        $this->licencePlate = $LicencePlate;

        return $this;
    }

    public function getGarage(): ?ParkingGarage
    {
        return $this->garage;
    }

    public function setGarage(?ParkingGarage $garage): static
    {
        $this->garage = $garage;

        return $this;
    }
}

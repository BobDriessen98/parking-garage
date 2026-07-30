<?php

namespace App\Entity;

use App\Repository\ParkingGarageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: ParkingGarageRepository::class)]
class ParkingGarage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?int $max_reservation_capacity = null;

    /**
     * @var Collection<int, ParkingSession>
     */
    #[ORM\OneToMany(targetEntity: ParkingSession::class, mappedBy: 'garage', orphanRemoval: true)]
    private Collection $parkingSessions;

    public function __construct()
    {
        $this->parkingSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getMaxReservationCapacity(): ?int
    {
        return $this->max_reservation_capacity;
    }

    public function setMaxReservationCapacity(?int $max_reservation_capacity): static
    {
        $this->max_reservation_capacity = $max_reservation_capacity;

        return $this;
    }

    /**
     * @return Collection<int, ParkingSession>
     */
    public function getParkingSessions(): Collection
    {
        return $this->parkingSessions;
    }

    public function addParkingSession(ParkingSession $parkingSession): static
    {
        if (!$this->parkingSessions->contains($parkingSession)) {
            $this->parkingSessions->add($parkingSession);
            $parkingSession->setGarage($this);
        }

        return $this;
    }

    public function removeParkingSession(ParkingSession $parkingSession): static
    {
        if ($this->parkingSessions->removeElement($parkingSession)) {
            // set the owning side to null (unless already changed)
            if ($parkingSession->getGarage() === $this) {
                $parkingSession->setGarage(null);
            }
        }

        return $this;
    }
}
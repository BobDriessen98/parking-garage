<?php

namespace App\Entity\ParkingSession\DTO\Input;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ParkingSessionEndInputDTO 
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type('integer')]
        public ?int $garageId,

        #[Assert\NotNull]
        #[Assert\Type('string')]
        public ?string $licencePlate,

        #[Assert\NotNull]
        #[Assert\Type(DateTimeImmutable::class)]
        public ?DateTimeImmutable $actualExit,
    ) {}
}
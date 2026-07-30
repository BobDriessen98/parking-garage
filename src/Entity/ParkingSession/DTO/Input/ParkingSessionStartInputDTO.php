<?php

namespace App\Entity\ParkingSession\DTO\Input;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ParkingSessionStartInputDTO 
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type('integer')]
        public ?int $garageId,

        #[Assert\NotNull]
        #[Assert\Length(min: 6, max: 10)]
        #[Assert\Type('string')]
        public ?string $licencePlate,

        #[Assert\NotNull]
        #[Assert\Type(DateTimeImmutable::class)]
        public ?DateTimeImmutable $actualEntry,
    ) {}
}
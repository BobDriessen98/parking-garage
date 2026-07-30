<?php

namespace App\Entity\ParkingSession\DTO\Output;

use DateTime;
use DateTimeImmutable;


final readonly class ParkingSessionDTO 
{
    public int $totalDurationSeconds;

    public function __construct(
        public string $licencePlate,
        public DateTimeImmutable $actualEntry,
        public ?DateTimeImmutable $actualExit,
    ) {
        $this->totalDurationSeconds = $actualExit 
            ? $actualExit->getTimestamp() - $actualEntry->getTimestamp() 
            : new DateTime()->getTimestamp()- $actualEntry->getTimestamp();
    }
}
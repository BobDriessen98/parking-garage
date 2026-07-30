<?php

namespace App\Message;

final readonly class SendEmailMessage
{
    public function __construct(
        public string $licencePlate,
        public string $action
    ) {
    }
}
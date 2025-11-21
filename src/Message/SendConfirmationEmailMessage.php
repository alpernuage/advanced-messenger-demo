<?php

declare(strict_types=1);

namespace App\Message;

final readonly class SendConfirmationEmailMessage
{
    public function __construct(
        public string $orderId,
    ) {
    }
}

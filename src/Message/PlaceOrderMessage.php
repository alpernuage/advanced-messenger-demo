<?php

declare(strict_types=1);

namespace App\Message;

final readonly class PlaceOrderMessage
{
    public function __construct(
        public string $orderId,
        public float $amount,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PlaceOrderMessage;
use App\Message\ProcessPaymentMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsMessageHandler]
final readonly class PlaceOrderHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(PlaceOrderMessage $message): void
    {
        $this->logger->info('📦 Commande placée', [
            'orderId' => $message->orderId,
            'amount' => $message->amount,
        ]);

        // Dispatch ProcessPaymentMessage avec un délai de 5 secondes
        $this->messageBus->dispatch(
            new ProcessPaymentMessage($message->orderId),
            [new DelayStamp(5000)]
        );

        $this->logger->info('⏰ Paiement programmé avec délai de 5s', [
            'orderId' => $message->orderId,
        ]);
    }
}

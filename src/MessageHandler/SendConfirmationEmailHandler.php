<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendConfirmationEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendConfirmationEmailHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendConfirmationEmailMessage $message): void
    {
        $this->logger->info('📧 Envoi de l\'email de confirmation', [
            'orderId' => $message->orderId,
        ]);

        // Simulation d'un travail
        sleep(1);

        $this->logger->info('✅ Email de confirmation envoyé', [
            'orderId' => $message->orderId,
        ]);
    }
}

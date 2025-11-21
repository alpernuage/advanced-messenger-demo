<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ProcessPaymentMessage;
use App\Message\SendConfirmationEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class ProcessPaymentHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ProcessPaymentMessage $message): void
    {
        // Simulation du compteur de tentatives
        static $attempts = [];
        $attempts[$message->orderId] = ($attempts[$message->orderId] ?? 0) + 1;
        $attempt = $attempts[$message->orderId];

        $this->logger->info('💳 Tentative de paiement', [
            'orderId' => $message->orderId,
            'attempt' => $attempt,
        ]);

        // Simulation d'échec (20% de chance)
        $shouldFail = rand(1, 100) <= 20;

        if ($shouldFail) {
            if ($attempt > 3) {
                // Erreur permanente -> DLQ
                $this->logger->error('❌ Échec permanent du paiement - envoi vers DLQ', [
                    'orderId' => $message->orderId,
                    'attempt' => $attempt,
                ]);

                throw new UnrecoverableMessageHandlingException(sprintf('Paiement échoué définitivement pour la commande %s', $message->orderId));
            }

            // Erreur temporaire -> retry
            $this->logger->warning('⚠️  Échec temporaire du paiement - retry programmé', [
                'orderId' => $message->orderId,
                'attempt' => $attempt,
            ]);

            throw new \RuntimeException(sprintf('Échec temporaire du paiement pour la commande %s', $message->orderId));
        }

        // Succès
        $this->logger->info('✅ Paiement réussi', [
            'orderId' => $message->orderId,
        ]);

        // Dispatch du message de confirmation
        $this->messageBus->dispatch(new SendConfirmationEmailMessage($message->orderId));
    }
}

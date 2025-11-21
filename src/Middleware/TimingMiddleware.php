<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final readonly class TimingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $messageClass = get_class($envelope->getMessage());

        $this->logger->info('⏳ Début du traitement', [
            'message' => $messageClass,
        ]);

        $startTime = microtime(true);

        try {
            $envelope = $stack->next()->handle($envelope, $stack);

            $duration = (microtime(true) - $startTime) * 1000;

            $this->logger->info(sprintf('✅ Message %s traité en %.2f ms', $messageClass, $duration), [
                'message' => $messageClass,
                'duration_ms' => round($duration, 2),
            ]);

            return $envelope;
        } catch (\Throwable $e) {
            $duration = (microtime(true) - $startTime) * 1000;

            $this->logger->error(sprintf('❌ Erreur lors du traitement de %s après %.2f ms', $messageClass, $duration), [
                'message' => $messageClass,
                'duration_ms' => round($duration, 2),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

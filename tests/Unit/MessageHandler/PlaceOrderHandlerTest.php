<?php

declare(strict_types=1);

namespace App\Tests\Unit\MessageHandler;

use App\Message\PlaceOrderMessage;
use App\Message\ProcessPaymentMessage;
use App\MessageHandler\PlaceOrderHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class PlaceOrderHandlerTest extends TestCase
{
    public function testHandlerDispatchesProcessPaymentMessageWithDelayStamp(): void
    {
        // Arrange
        $orderId = 'ORDER-123';
        $amount = 99.99;
        $message = new PlaceOrderMessage($orderId, $amount);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))
            ->method('info');

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function ($msg) use ($orderId) {
                    return $msg instanceof ProcessPaymentMessage
                        && $msg->orderId === $orderId;
                }),
                $this->callback(function ($stamps) {
                    // Vérifier qu'il y a exactement un DelayStamp de 5000ms
                    $this->assertIsArray($stamps);
                    $this->assertCount(1, $stamps);
                    $this->assertInstanceOf(DelayStamp::class, $stamps[0]);
                    $this->assertSame(5000, $stamps[0]->getDelay());

                    return true;
                })
            )
            ->willReturn(new Envelope(new ProcessPaymentMessage($orderId)));

        $handler = new PlaceOrderHandler($messageBus, $logger);

        // Act
        $handler($message);

        // Assert - les expectations du mock sont vérifiées automatiquement
    }
}

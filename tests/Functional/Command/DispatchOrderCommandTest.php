<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Message\PlaceOrderMessage;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class DispatchOrderCommandTest extends KernelTestCase
{
    public function testCommandDispatchesPlaceOrderMessage(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $messageBusMock = $this->createMock(MessageBusInterface::class);
        $messageBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PlaceOrderMessage::class))
            ->willReturnCallback(function (PlaceOrderMessage $message) {
                // Vérifier que le message a bien un orderId et un amount
                $this->assertStringStartsWith('ORDER-', $message->orderId);
                $this->assertGreaterThan(0, $message->amount);
                $this->assertLessThanOrEqual(500, $message->amount);

                return new Envelope($message);
            });

        // Remplacer le service dans le conteneur
        self::getContainer()->set(MessageBusInterface::class, $messageBusMock);

        $command = $application->find('app:dispatch-order');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $this->assertSame(0, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Commande dispatchée', $output);
        $this->assertStringContainsString('ORDER-', $output);
    }
}

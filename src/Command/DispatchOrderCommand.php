<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\PlaceOrderMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:dispatch-order',
    description: 'Dispatche une commande de test pour démontrer Messenger',
)]
final class DispatchOrderCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $orderId = 'ORDER-'.uniqid();
        $amount = round(rand(1000, 50000) / 100, 2);

        $message = new PlaceOrderMessage($orderId, $amount);

        $this->messageBus->dispatch($message);

        $io->success(sprintf(
            'Commande dispatchée : %s (Montant: %.2f€)',
            $orderId,
            $amount
        ));

        return Command::SUCCESS;
    }
}

<?php

namespace App\MessageHandler;

use App\Message\ShippingRequested;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ShippingRequestedHandler
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(ShippingRequested $message): void
    {
        $this->logger->info('Orchestration expédition demandée', [
            'order' => $message->getOrderNumber(),
        ]);
    }
}

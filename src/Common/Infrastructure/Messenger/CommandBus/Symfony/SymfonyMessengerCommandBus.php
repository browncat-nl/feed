<?php

namespace App\Common\Infrastructure\Messenger\CommandBus\Symfony;

use App\Common\Infrastructure\Messenger\CommandBus\CommandBus;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsAlias(id: CommandBus::class)]
class SymfonyMessengerCommandBus implements CommandBus
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function handle(object $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $handlerFailedException) {
            $exceptions = $handlerFailedException->getNestedExceptions();

            $firstException = reset($exceptions);

            throw $firstException !== false ? $firstException : $handlerFailedException;
        }
    }
}

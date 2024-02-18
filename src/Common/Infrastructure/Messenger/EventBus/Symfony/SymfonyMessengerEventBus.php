<?php

namespace App\Common\Infrastructure\Messenger\EventBus\Symfony;

use App\Common\Infrastructure\Messenger\EventBus\EventBus;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsAlias(EventBus::class)]
class SymfonyMessengerEventBus implements EventBus
{
    public function __construct(private MessageBusInterface $eventBus)
    {
    }

    public function dispatch(object $event): void
    {
        try {
            $this->eventBus->dispatch($event);
        } catch (HandlerFailedException $handlerFailedException) {
            $exceptions = $handlerFailedException->getWrappedExceptions();

            $firstException = reset($exceptions);

            throw $firstException !== false ? $firstException : $handlerFailedException;
        }
    }
}

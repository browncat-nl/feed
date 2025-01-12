<?php

namespace App\Feed\Application\Listener\Feed;

use App\Common\Infrastructure\Messenger\CommandBus\CommandBus;
use App\Common\Infrastructure\Messenger\EventBus\AsEventSubscriber;
use App\Feed\Application\Command\Feed\NormalizeFeedItemCommand;
use App\Feed\Application\Event\Feed\FeedItemWasFetchedEvent;

final readonly class NormalizeFeedItemWhenFetchedListener
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    #[AsEventSubscriber]
    public function onFeedItemWasFetchedEvent(FeedItemWasFetchedEvent $event): void
    {
        $this->commandBus->handle(new NormalizeFeedItemCommand($event->feedItem));
    }
}

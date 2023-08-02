<?php

namespace App\Feed\Application\Listener\Article;

use App\Common\Infrastructure\Messenger\CommandBus\CommandBus;
use App\Common\Infrastructure\Messenger\EventBus\AsEventSubscriber;
use App\Feed\Application\Command\Article\UpsertArticleCommand;
use App\Feed\Application\Event\Feed\FeedItemWasFetchedEvent;

final readonly class UpsertArticleOnFeedItemFetchedListener
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    #[AsEventSubscriber]
    public function onFeedItemWasFetchedEvent(FeedItemWasFetchedEvent $event): void
    {
        $feedItem = $event->feedItem;

        $this->commandBus->handle(new UpsertArticleCommand(
            $feedItem->title,
            $feedItem->summary,
            $feedItem->url,
            $feedItem->updated,
            $feedItem->source
        ));
    }
}

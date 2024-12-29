<?php

namespace App\Feed\Application\Command\Feed\Handler;

use App\Common\Infrastructure\Messenger\CommandBus\AsCommandHandler;
use App\Common\Infrastructure\Messenger\EventBus\EventBus;
use App\Feed\Application\Command\Feed\FetchFeedCommand;
use App\Feed\Application\Event\Feed\FeedItemWasFetchedEvent;
use App\Feed\Application\FeedParser\FeedParser;
use App\Feed\Domain\Source\SourceId;
use App\Feed\Domain\Source\SourceRepository;

final readonly class FetchFeedHandler
{
    public function __construct(
        private SourceRepository $sourceRepository,
        private FeedParser $feedParser,
        private EventBus $eventBus,
    ) {
    }

    #[AsCommandHandler]
    public function __invoke(FetchFeedCommand $command): void
    {
        $source = $this->sourceRepository->findOrThrow(new SourceId($command->sourceId));

        $feedItems = $this->feedParser->fetchFeed($source->getName(), $source->getUrl());

        foreach ($feedItems as $feedItem) {
            $this->eventBus->dispatch(
                new FeedItemWasFetchedEvent($feedItem)
            );
        }
    }
}

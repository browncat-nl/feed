<?php

namespace App\Feed\Application\Command\Feed\Handler;

use App\Common\Infrastructure\Messenger\CommandBus\AsCommandHandler;
use App\Common\Infrastructure\Messenger\EventBus\EventBus;
use App\Feed\Application\Command\Feed\NormalizeFeedItemCommand;
use App\Feed\Application\Event\Feed\FeedItemWasNormalizedEvent;
use App\Feed\Application\Service\FeedItemNormalizer\FeedItemNormalizer;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommandHandler]
final readonly class NormalizeFeedItemHandler
{
    /**
     * @param iterable<FeedItemNormalizer> $feedItemNormalizers
     */
    public function __construct(
        #[AutowireIterator(FeedItemNormalizer::class)]
        private iterable $feedItemNormalizers,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(NormalizeFeedItemCommand $command): void
    {
        $feedItem = $command->feedItem;

        foreach ($this->feedItemNormalizers as $feedItemNormalizer) {
            $feedItem = $feedItemNormalizer->__invoke($feedItem);
        }

        $this->eventBus->dispatch(new FeedItemWasNormalizedEvent($feedItem));
    }
}

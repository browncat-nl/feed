<?php

namespace App\Feed\Application\Command\Feed\Handler;

use App\Common\Infrastructure\Messenger\CommandBus\AsCommandHandler;
use App\Common\Infrastructure\Messenger\EventBus\EventBus;
use App\Feed\Application\Command\Feed\FetchFeedCommand;
use App\Feed\Application\Event\Feed\FeedItemWasFetchedEvent;
use App\Feed\Application\Exception\Feed\FeedCouldNotBeProvidedException;
use App\Feed\Application\Service\FeedProvider\FeedProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceProviderInterface;

final readonly class FetchFeedHandler
{
    /**
     * @param ServiceProviderInterface<FeedProvider> $feedProviders
     */
    public function __construct(
        #[AutowireLocator(FeedProvider::class, defaultIndexMethod: 'getSource')]
        private ServiceProviderInterface $feedProviders,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @throws FeedCouldNotBeProvidedException
     */
    #[AsCommandHandler]
    public function __invoke(FetchFeedCommand $command): void
    {
        if ($this->feedProviders->has($command->source) === false) {
            throw FeedCouldNotBeProvidedException::withNonExistingSource($command->source);
        }

        $feedProvider = $this->feedProviders->get($command->source);

        foreach ($feedProvider->fetchFeedItems() as $feedItem) {
            $this->eventBus->dispatch(
                new FeedItemWasFetchedEvent($feedItem)
            );
        }
    }
}

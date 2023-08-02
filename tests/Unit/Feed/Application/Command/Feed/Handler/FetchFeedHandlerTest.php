<?php

namespace Unit\Feed\Application\Command\Feed\Handler;

use App\Feed\Application\Command\Article\Handler\UpsertArticleHandler;
use App\Feed\Application\Command\Feed\FetchFeedCommand;
use App\Feed\Application\Command\Feed\Handler\FetchFeedHandler;
use App\Feed\Application\Event\Feed\FeedItemWasFetchedEvent;
use App\Feed\Application\Exception\Feed\FeedCouldNotBeProvidedException;
use App\Feed\Application\Service\FeedProvider\FeedItem;
use App\Feed\Application\Service\FeedProvider\FeedProvider;
use Dev\Common\Infrastructure\Logger\InMemoryLogger;
use Dev\Common\Infrastructure\Messenger\EventBus\RecordingEventBus;
use Dev\Feed\Repository\InMemoryArticleRepository;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Service\ServiceLocatorTrait;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class FetchFeedHandlerTest extends TestCase
{
    private FetchFeedHandler $handler;
    private RecordingEventBus $eventBus;

    public function setUp(): void
    {
        $feedProviderServiceLocator = new class ([
            'dummy_feed_1' => new class implements FeedProvider {
                public function __invoke(): self
                {
                    return $this;
                }

                public static function getSource(): string
                {
                    return 'dummy_feed_1';
                }

                public function fetchFeedItems(): array
                {
                    return [
                        new FeedItem(
                            'test title 1.1',
                            'test summary 1.1',
                            'https://example.com/test-title-1-1',
                            new \DateTime('2022-03-03 00:00:00'),
                            self::getSource(),
                        ),
                        new FeedItem(
                            'test title 1.2',
                            'test summary 1.2',
                            'https://example.com/test-title-1-2',
                            new \DateTime('2022-03-03 00:00:00'),
                            self::getSource(),
                        )
                    ];
                }
            }
        ]) implements
            ServiceProviderInterface
        {
            use ServiceLocatorTrait;
        };

        $this->eventBus = new RecordingEventBus();

        $this->handler = new FetchFeedHandler(
            $feedProviderServiceLocator,
            $this->eventBus,
        );
    }

    /**
     * @test
     */
    public function it_should_retrieve_the_feed(): void
    {
        // Arrange
        $command = new FetchFeedCommand('dummy_feed_1');

        // Act
        $this->handler->__invoke($command);

        // Assert
        $event = $this->eventBus->shiftEvent();

        self::assertInstanceOf(FeedItemWasFetchedEvent::class, $event);

        self::assertEquals('test title 1.1', $event->feedItem->title);
        self::assertEquals('test summary 1.1', $event->feedItem->summary);
        self::assertEquals('https://example.com/test-title-1-1', $event->feedItem->url);
        self::assertEquals(new \DateTime('2022-03-03 00:00:00'), $event->feedItem->updated);
        self::assertEquals('dummy_feed_1', $event->feedItem->source);

        $event2 = $this->eventBus->shiftEvent();

        self::assertInstanceOf(FeedItemWasFetchedEvent::class, $event2);

        self::assertEquals('test title 1.2', $event2->feedItem->title);
        self::assertEquals('test summary 1.2', $event2->feedItem->summary);
        self::assertEquals('https://example.com/test-title-1-2', $event2->feedItem->url);
        self::assertEquals(new \DateTime('2022-03-03 00:00:00'), $event2->feedItem->updated);
        self::assertEquals('dummy_feed_1', $event2->feedItem->source);

        self::assertTrue($this->eventBus->isEmpty());
    }

    /**
     * @test
     */
    public function it_should_throw_if_feed_could_not_be_found(): void
    {
        // Assert
        self::expectExceptionObject(FeedCouldNotBeProvidedException::withNonExistingSource('non-existing-source'));

        // Arrange
        $command = new FetchFeedCommand('non-existing-source');

        // Act
        $this->handler->__invoke($command);
    }
}

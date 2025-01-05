<?php

namespace Unit\Feed\Application\Command\Feed\Handler;

use App\Feed\Application\Command\Feed\FetchFeedCommand;
use App\Feed\Application\Command\Feed\Handler\FetchFeedHandler;
use App\Feed\Application\Event\Feed\FeedItemWasFetchedEvent;
use App\Feed\Application\Service\FeedFetcher\FeedFetcher;
use App\Feed\Application\Service\FeedFetcher\FeedItem;
use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use App\Feed\Domain\Source\SourceId;
use Dev\Common\Infrastructure\Messenger\EventBus\RecordingEventBus;
use Dev\Feed\Factory\SourceFactory;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;

final class FetchFeedHandlerTest extends TestCase
{
    private FetchFeedHandler $handler;
    private InMemorySourceRepository $sourceRepository;
    private RecordingEventBus $eventBus;

    public function setUp(): void
    {
        $feedFetcher = new class implements FeedFetcher {
            public function __invoke(string $source, string $url): array
            {
                return [
                    new FeedItem(
                        'test title 1.1',
                        'test summary 1.1',
                        'https://example.com/test-title-1-1',
                        new \DateTime('2022-03-03 00:00:00'),
                        $source,
                    ),
                    new FeedItem(
                        'test title 1.2',
                        'test summary 1.2',
                        'https://example.com/test-title-1-2',
                        new \DateTime('2022-03-03 00:00:00'),
                        $source,
                    )
                ];
            }
        };


        $this->sourceRepository = new InMemorySourceRepository();
        $this->eventBus = new RecordingEventBus();

        $this->handler = new FetchFeedHandler(
            $this->sourceRepository,
            $feedFetcher,
            $this->eventBus,
        );
    }

    /**
     * @test
     */
    public function it_should_retrieve_the_feed(): void
    {
        // Arrange
        $source = SourceFactory::setup()->create();
        $this->sourceRepository->save($source);

        $command = new FetchFeedCommand((string) $source->getId());

        // Act
        $this->handler->__invoke($command);

        // Assert
        $event = $this->eventBus->shiftEvent();

        self::assertInstanceOf(FeedItemWasFetchedEvent::class, $event);

        self::assertEquals('test title 1.1', $event->feedItem->title);
        self::assertEquals('test summary 1.1', $event->feedItem->summary);
        self::assertEquals('https://example.com/test-title-1-1', $event->feedItem->url);
        self::assertEquals(new \DateTime('2022-03-03 00:00:00'), $event->feedItem->updated);
        self::assertEquals($source->getName(), $event->feedItem->source);

        $event2 = $this->eventBus->shiftEvent();

        self::assertInstanceOf(FeedItemWasFetchedEvent::class, $event2);

        self::assertEquals('test title 1.2', $event2->feedItem->title);
        self::assertEquals('test summary 1.2', $event2->feedItem->summary);
        self::assertEquals('https://example.com/test-title-1-2', $event2->feedItem->url);
        self::assertEquals(new \DateTime('2022-03-03 00:00:00'), $event2->feedItem->updated);
        self::assertEquals($source->getName(), $event2->feedItem->source);

        self::assertTrue($this->eventBus->isEmpty());
    }

    /**
     * @test
     */
    public function it_should_throw_if_the_source_does_not_exist(): void
    {
        // Assert
        self::expectExceptionObject(SourceNotFoundException::withSourceId(new SourceId('adc155d7-319f-400b-9321-c07a0f073ba0')));

        // Arrange
        $command = new FetchFeedCommand('adc155d7-319f-400b-9321-c07a0f073ba0');

        // Act
        $this->handler->__invoke($command);
    }
}

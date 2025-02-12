<?php

namespace Unit\Feed\Application\Listener\Feed;

use App\Common\Infrastructure\Messenger\CommandBus\CommandBus;
use App\Feed\Application\Command\Feed\NormalizeFeedItemCommand;
use App\Feed\Application\Event\Feed\FeedItemWasFetchedEvent;
use App\Feed\Application\Listener\Feed\NormalizeFeedItemWhenFetchedListener;
use App\Feed\Application\Service\FeedFetcher\FeedItem;
use Dev\Common\Infrastructure\Messenger\CommandBus\RecordingCommandBus;
use PHPUnit\Framework\TestCase;

final class NormalizeFeedItemWhenFetchedListenerTest extends TestCase
{
    private RecordingCommandBus $commandBus;
    private NormalizeFeedItemWhenFetchedListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = new RecordingCommandBus();
        $this->listener = new NormalizeFeedItemWhenFetchedListener($this->commandBus);
    }

    /**
     * @test
     */
    public function it_should_normalize_the_fetched_feed_item(): void
    {
        // Arrange
        $event = new FeedItemWasFetchedEvent(
            $feedItem = new FeedItem(
                'test title 1.1',
                'test summary 1.1',
                'https://example.com/test-title-1-1',
                new \DateTime('2022-03-03 00:00:00'),
                'some source',
            )
        );

        // Act
        $this->listener->onFeedItemWasFetchedEvent($event);

        // Assert
        $command = $this->commandBus->shiftCommand();

        self::assertInstanceOf(NormalizeFeedItemCommand::class, $command);
        self::assertSame($feedItem, $command->feedItem);
    }
}

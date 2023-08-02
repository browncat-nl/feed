<?php

namespace Unit\Feed\Application\Listener\Article;

use App\Feed\Application\Command\Article\UpsertArticleCommand;
use App\Feed\Application\Event\Feed\FeedItemWasFetchedEvent;
use App\Feed\Application\Listener\Article\UpsertArticleOnFeedItemFetchedListener;
use App\Feed\Application\Service\FeedProvider\FeedItem;
use Dev\Common\Infrastructure\Messenger\CommandBus\RecordingCommandBus;
use PHPUnit\Framework\TestCase;

final class UpsertArticleOnFeedItemFetchedListenerTest extends TestCase
{
    private UpsertArticleOnFeedItemFetchedListener $listener;
    private RecordingCommandBus $commandBus;

    protected function setUp(): void
    {
        $this->commandBus = new RecordingCommandBus();
        $this->listener = new UpsertArticleOnFeedItemFetchedListener($this->commandBus);
    }

    /**
     * @test
     */
    public function it_should_dispatch_an_upsert_article_command_on_event(): void
    {
        // Arrange
        $feedItem = new FeedItem(
            'test title 1.1',
            'test summary 1.1',
            'https://example.com/test-title-1-1',
            new \DateTime('2022-03-03 00:00:00'),
            'test-source',
        );

        $event = new FeedItemWasFetchedEvent($feedItem);

        // Act
        $this->listener->onFeedItemWasFetchedEvent($event);

        // Assert
        $command = $this->commandBus->shiftCommand();

        self::assertInstanceOf(UpsertArticleCommand::class, $command);

        self::assertEquals($command->title, $feedItem->title);
        self::assertEquals($command->url, $feedItem->url);
        self::assertEquals($command->updated, $feedItem->updated);
        self::assertEquals($command->summary, $feedItem->summary);
        self::assertEquals($command->sourceName, $feedItem->source);

        self::assertTrue($this->commandBus->isEmpty());
    }
}

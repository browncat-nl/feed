<?php

namespace Unit\Feed\Application\Command\Feed\Handler;

use App\Feed\Application\Command\Feed\Handler\NormalizeFeedItemHandler;
use App\Feed\Application\Command\Feed\NormalizeFeedItemCommand;
use App\Feed\Application\Event\Feed\FeedItemWasNormalizedEvent;
use App\Feed\Application\Service\FeedFetcher\FeedItem;
use App\Feed\Application\Service\FeedItemNormalizer\FeedItemNormalizer;
use Dev\Common\Infrastructure\Messenger\EventBus\RecordingEventBus;
use PHPUnit\Framework\TestCase;

final class NormalizeFeedItemHandlerTest extends TestCase
{
    private NormalizeFeedItemHandler $handler;
    private RecordingEventBus $eventBus;

    protected function setUp(): void
    {
        $this->eventBus = new RecordingEventBus();

        $badWordNormalizer = new class () implements FeedItemNormalizer
        {
            public function __invoke(FeedItem $feedItem): FeedItem
            {
                return new FeedItem(
                    $feedItem->title,
                    str_replace("bad word", "***", $feedItem->summary),
                    $feedItem->url,
                    $feedItem->updated,
                    $feedItem->source,
                );
            }
        };

        $this->handler = new NormalizeFeedItemHandler([$badWordNormalizer], $this->eventBus);
    }

    /**
     * @test
     */
    public function it_should_normalize_a_feed_item(): void
    {
        // Arrange
        $feedItem = new FeedItem(
            'test title',
            'summary containing a bad word',
            'https://example.com/test-title',
            new \DateTime('2022-03-03 00:00:00'),
            'test-source',
        );

        // Act
        $this->handler->__invoke(new NormalizeFeedItemCommand($feedItem));

        // Assert
        $event = $this->eventBus->shiftEvent();

        self::assertInstanceOf(FeedItemWasNormalizedEvent::class, $event);

        self::assertSame('summary containing a ***', $event->feedItem->summary);
    }
}

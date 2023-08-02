<?php

namespace Unit\Feed\Infrastructure\Console;

use App\Feed\Application\Command\Article\UpsertArticleCommand;
use App\Feed\Application\Service\FeedProvider\FeedItem;
use App\Feed\Application\Service\FeedProvider\FeedProvider;
use App\Feed\Infrastructure\Console\FetchExternalFeedsCLICommand;
use Dev\Common\Infrastructure\Logger\InMemoryLogger;
use Dev\Common\Infrastructure\Messenger\CommandBus\RecordingCommandBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class FetchExternalFeedsCLICommandTest extends TestCase
{
    private RecordingCommandBus $commandBus;
    private LoggerInterface $logger;
    private FeedProvider $dummyFeedProvider1;
    private FeedProvider $dummyFeedProvider2;


    public function setUp(): void
    {
        $this->commandBus = new RecordingCommandBus();
        $this->logger = new InMemoryLogger();

        $this->dummyFeedProvider1 = new class implements FeedProvider {
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
                    )
                ];
            }
        };

        $this->dummyFeedProvider2 = new class implements FeedProvider {
            public static function getSource(): string
            {
                return 'dummy_feed_2';
            }

            public function fetchFeedItems(): array
            {
                return [
                    new FeedItem(
                        'test title 2.1',
                        'test summary 2.1',
                        'https://example.com/test-title-2-1',
                        new \DateTime('2022-03-03 00:00:00'),
                        self::getSource(),
                    )
                ];
            }
        };
    }

    /**
     * @test
     */
    public function it_should_retrieve_and_store_the_feed(): void
    {
        // Arrange
        $command = new FetchExternalFeedsCLICommand(
            [
                $this->dummyFeedProvider1,
                $this->dummyFeedProvider2,
            ],
            $this->commandBus,
            $this->logger,
        );

        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        self::assertEquals(new UpsertArticleCommand(
            'test title 1.1',
            'test summary 1.1',
            'https://example.com/test-title-1-1',
            new \DateTime('2022-03-03 00:00:00'),
            'dummy_feed_1'
        ), $this->commandBus->shiftCommand());

        self::assertEquals(new UpsertArticleCommand(
            'test title 2.1',
            'test summary 2.1',
            'https://example.com/test-title-2-1',
            new \DateTime('2022-03-03 00:00:00'),
            'dummy_feed_2'
        ), $this->commandBus->shiftCommand());

        self::assertTrue($this->commandBus->isEmpty());
    }
}

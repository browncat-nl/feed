<?php

namespace Unit\Feed\Infrastructure\Console;

use App\Feed\Application\Command\Feed\FetchFeedCommand;
use App\Feed\Application\Query\Source\Handler\GetAllSourceIdsHandler;
use App\Feed\Infrastructure\Console\FetchExternalFeedsCLICommand;
use Dev\Common\Infrastructure\Logger\InMemoryLogger;
use Dev\Common\Infrastructure\Messenger\CommandBus\RecordingCommandBus;
use Dev\Feed\Factory\SourceFactory;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class FetchExternalFeedsCLICommandTest extends TestCase
{
    private RecordingCommandBus $commandBus;
    private LoggerInterface $logger;
    private InMemorySourceRepository $sourceRepository;


    public function setUp(): void
    {
        $this->commandBus = new RecordingCommandBus();
        $this->logger = new InMemoryLogger();
        $this->sourceRepository = new InMemorySourceRepository();
    }

    /**
     * @test
     */
    public function it_should_dispatch_the_fetch_feed_command_for_all_sources(): void
    {
        // Arrange
        $source1 = SourceFactory::setup()->create();
        $source2 = SourceFactory::setup()->create();

        $this->sourceRepository->save($source1, $source2);

        $command = new FetchExternalFeedsCLICommand(
            new GetAllSourceIdsHandler($this->sourceRepository),
            $this->commandBus,
            $this->logger,
        );

        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        self::assertEquals(new FetchFeedCommand(
            (string) $source1->getId(),
        ), $this->commandBus->shiftCommand());

        self::assertEquals(new FetchFeedCommand(
            (string) $source2->getId(),
        ), $this->commandBus->shiftCommand());

        self::assertTrue($this->commandBus->isEmpty());
    }
}

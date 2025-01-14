<?php

namespace Unit\Source;

use App\Feed\Application\Command\Source\AddSourceCommand;
use App\Feed\Application\Command\Source\Handler\AddSourceHandler;
use App\Feed\Domain\Source\SourceId;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;

final class AddSourceHandlerTest extends TestCase
{
    private InMemorySourceRepository $sourceRepository;
    private AddSourceHandler $handler;

    public function setUp(): void
    {
        parent::setUp();

        $this->sourceRepository = new InMemorySourceRepository();
        $this->handler = new AddSourceHandler($this->sourceRepository);
    }

    /**
     * @test
     */
    public function it_should_add_the_source(): void
    {
        // Arrange
        $command = new AddSourceCommand('3fd4560c-6bc5-4c41-aafa-6087ea0fc820', 'some-source', 'https://example.com/some-source');

        // Act
        $this->handler->__invoke($command);

        // Assert
        $source = $this->sourceRepository->find(new SourceId('3fd4560c-6bc5-4c41-aafa-6087ea0fc820'));

        self::assertNotNull($source);
        self::assertSame('some-source', $source->getName());
        self::assertSame('https://example.com/some-source', (string) $source->getUrl());
    }
}

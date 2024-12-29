<?php

namespace Unit\Feed\Application\Query\Source\Handler;

use App\Feed\Application\Query\Source\GetAllSourceIdsQuery;
use App\Feed\Application\Query\Source\Handler\GetAllSourceIdsHandler;
use Dev\Feed\Factory\SourceFactory;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;

final class GetAllSourceIdsHandlerTest extends TestCase
{
    private GetAllSourceIdsHandler $handler;
    private InMemorySourceRepository $sourceRepository;

    protected function setUp(): void
    {
        $this->sourceRepository = new InMemorySourceRepository();
        $this->handler = new GetAllSourceIdsHandler($this->sourceRepository);

        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_empty_list_if_there_are_no_sources(): void
    {
        // Act
        $sourceIds = $this->handler->__invoke(new GetAllSourceIdsQuery());

        // Assert
        self::assertCount(0, $sourceIds);
    }

    /**
     * @test
     */
    public function it_should_retrieve_all_the_source_ids(): void
    {
        // Arrange
        $source1 = SourceFactory::setup()->create();
        $source2 = SourceFactory::setup()->create();
        $source3 = SourceFactory::setup()->create();

        $this->sourceRepository->save($source1, $source2, $source3);

        // Act
        $sourceIds = $this->handler->__invoke(new GetAllSourceIdsQuery());

        // Assert
        self::assertCount(3, $sourceIds);

        self::assertSame([(string) $source1->getId(), (string) $source2->getId(), (string) $source3->getId()], $sourceIds);
    }
}

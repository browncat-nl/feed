<?php

namespace Unit\Source;

use App\Feed\Application\Command\Source\AddSourceCommand;
use App\Feed\Application\Command\Source\Handler\AddSourceHandler;
use App\Feed\Domain\Category\Exception\CategoryNotFoundException;
use App\Feed\Domain\Source\SourceId;
use Dev\Feed\Factory\CategoryFactory;
use Dev\Feed\Repository\InMemoryCategoryRepository;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;

final class AddSourceHandlerTest extends TestCase
{
    private InMemorySourceRepository $sourceRepository;
    private InMemoryCategoryRepository $categoryRepository;
    private AddSourceHandler $handler;

    public function setUp(): void
    {
        parent::setUp();

        $this->sourceRepository = new InMemorySourceRepository();
        $this->categoryRepository = new InMemoryCategoryRepository();
        $this->handler = new AddSourceHandler($this->sourceRepository, $this->categoryRepository);
    }

    /**
     * @test
     */
    public function it_should_throw_if_given_category_does_not_exist(): void
    {
        // Arrange
        $command = new AddSourceCommand('3fd4560c-6bc5-4c41-aafa-6087ea0fc820', 'some-source', 'https://example.com/some-source', 'non-existing-category');

        // Assert
        self::expectExceptionObject(CategoryNotFoundException::withName('non-existing-category'));

        // Act
        $this->handler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_should_add_the_source(): void
    {
        // Arrange
        $this->categoryRepository->save($category = CategoryFactory::setup()->create());

        $command = new AddSourceCommand('3fd4560c-6bc5-4c41-aafa-6087ea0fc820', 'some-source', 'https://example.com/some-source', $category->getName());

        // Act
        $this->handler->__invoke($command);

        // Assert
        $source = $this->sourceRepository->find(new SourceId('3fd4560c-6bc5-4c41-aafa-6087ea0fc820'));

        self::assertNotNull($source);
        self::assertSame('some-source', $source->getName());
        self::assertSame('https://example.com/some-source', (string) $source->getUrl());
    }
}

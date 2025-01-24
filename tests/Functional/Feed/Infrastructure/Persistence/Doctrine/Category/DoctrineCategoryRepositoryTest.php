<?php

namespace Functional\Feed\Infrastructure\Persistence\Doctrine\Category;

use App\Feed\Domain\Category\Exception\CategoryNotFoundException;
use App\Feed\Infrastructure\Persistence\Doctrine\Category\DoctrineCategoryRepository;
use Dev\Feed\Factory\CategoryFactory;
use Dev\Testing\PHPUnit\DoctrineTestCase;

class DoctrineCategoryRepositoryTest extends DoctrineTestCase
{
    private DoctrineCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new DoctrineCategoryRepository($this->getDoctrine());
    }

    /**
     * @test
     */
    public function it_should_find_by_name(): void
    {
        // Arrange
        $this->repository->save($category = CategoryFactory::setup()->create());
        $this->getDoctrine()->resetManager();

        // Act
        $foundCategory = $this->repository->findByNameOrThrow($category->getName());

        // Assert
        self::assertEquals($category->getId(), $foundCategory->getId());
    }

    /**
     * @test
     */
    public function it_should_throw_if_it_cant_find_by_name(): void
    {
        // Assert
        self::expectException(CategoryNotFoundException::class);

        // Act
        $this->repository->findByNameOrThrow('non-existing-category');
    }
}

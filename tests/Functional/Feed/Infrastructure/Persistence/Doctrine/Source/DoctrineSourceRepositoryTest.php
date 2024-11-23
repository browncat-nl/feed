<?php

namespace Functional\Feed\Infrastructure\Persistence\Doctrine\Source;

use App\Feed\Domain\Source\Exception\SourceNotFoundException;
use App\Feed\Infrastructure\Persistence\Doctrine\Source\DoctrineSourceRepository;
use Dev\Feed\Factory\SourceFactory;
use Dev\Testing\PHPUnit\DoctrineTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineSourceRepositoryTest extends DoctrineTestCase
{
    private DoctrineSourceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new DoctrineSourceRepository($this->getDoctrine());
    }

    /**
     * @test
     */
    public function it_should_find_the_source_by_its_name(): void
    {
        // Arrange
        $source = SourceFactory::setup()->create();

        $this->repository->save($source);
        $this->getDoctrine()->resetManager();

        // Act
        $foundSource = $this->repository->findByNameOrThrow($source->getName());

        // Assert
        self::assertEquals($source, $foundSource);
    }

    /**
     * @test
     */
    public function it_should_throw_if_the_source_cant_be_find_by_name(): void
    {
        // Assert
        $this->expectExceptionObject(SourceNotFoundException::withName('non-existing'));

        // Act
        $this->repository->findByNameOrThrow('non-existing');
    }
}

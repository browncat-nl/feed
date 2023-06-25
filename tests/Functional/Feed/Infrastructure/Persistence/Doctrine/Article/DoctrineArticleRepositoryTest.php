<?php

namespace Functional\Feed\Infrastructure\Persistence\Doctrine\Article;

use App\Feed\Infrastructure\Persistence\Doctrine\Article\DoctrineArticleRepository;
use DateTime;
use Dev\Feed\Factory\ArticleFactory;
use Dev\Feed\Factory\SourceFactory;
use Dev\Testing\PHPUnit\DoctrineTestCase;

class DoctrineArticleRepositoryTest extends DoctrineTestCase
{
    private DoctrineArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new DoctrineArticleRepository($this->getDoctrine());
    }

    /**
     * @test
     */
    public function it_should_return_the_latest_articles_in_a_sorted_manner(): void
    {
        // Arrange
        $source = (new SourceFactory())->create();
        $this->getDoctrine()->getManager()->persist($source);

        $article1 = (new ArticleFactory())->withSource($source)->withUpdated(new DateTime('2021-03-10 00:10:00'))->create();
        $article2 = (new ArticleFactory())->withSource($source)->withUpdated(new DateTime('2020-03-10 00:00:00'))->create();
        $article3 = (new ArticleFactory())->withSource($source)->withUpdated(new DateTime('2021-03-10 00:00:00'))->create();
        $article4 = (new ArticleFactory())->withSource($source)->withUpdated(new DateTime('2020-03-10 00:00:00'))->create();

        $this->repository->save($article1, $article2, $article3, $article4);
        $this->getDoctrine()->resetManager();

        // Act
        $articles = $this->repository->findLatest(2);

        // Assert
        self::assertCount(2, $articles);

        self::assertEquals($article1->getId(), $articles[0]->getId());
        self::assertEquals($article3->getId(), $articles[1]->getId());
    }
}

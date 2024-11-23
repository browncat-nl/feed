<?php

namespace Functional\Feed\Infrastructure\Persistence\Doctrine\Article;

use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\Exception\ArticleNotFoundException;
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
        $article1 = ArticleFactory::setup()->withUpdated(new DateTime('2021-03-10 00:10:00'))->create();
        $article2 = ArticleFactory::setup()->withUpdated(new DateTime('2020-03-10 00:00:00'))->create();
        $article3 = ArticleFactory::setup()->withUpdated(new DateTime('2021-03-10 00:00:00'))->create();
        $article4 = ArticleFactory::setup()->withUpdated(new DateTime('2020-03-10 00:00:00'))->create();

        $this->repository->save($article1, $article2, $article3, $article4);
        $this->getDoctrine()->resetManager();

        // Act
        $articles = $this->repository->findLatestIds(0, 2);

        // Assert
        self::assertCount(2, $articles);

        self::assertEquals($article1->getId(), $articles[0]);
        self::assertEquals($article3->getId(), $articles[1]);
    }

    /**
     * @test
     */
    public function it_should_find_the_article_by_its_url(): void
    {
        // Arrange
        $article = ArticleFactory::setup()->create();

        $this->repository->save($article);
        $this->getDoctrine()->resetManager();

        // Act
        $foundArticle = $this->repository->findByUrl($article->getUrl());

        // Assert
        self::assertNotNull($foundArticle);
        self::assertEquals($article->getId(), $foundArticle->getId());
    }

    /**
     * @test
     */
    public function it_should_return_null_if_there_is_no_article_with_given_url(): void
    {
        // Act
        $foundArticle = $this->repository->findByUrl('http://non-existing.com/non-existing');

        // Assert
        self::assertNull($foundArticle);
    }

    /**
     * @test
     */
    public function it_should_count_the_articles(): void
    {
        // Arrange
        $article1 = ArticleFactory::setup()->withUpdated(new DateTime('2021-03-10 00:10:00'))->create();
        $article2 = ArticleFactory::setup()->withUpdated(new DateTime('2020-03-10 00:00:00'))->create();
        $article3 = ArticleFactory::setup()->withUpdated(new DateTime('2021-03-10 00:00:00'))->create();

        $this->repository->save($article1, $article2, $article3);
        $this->getDoctrine()->resetManager();

        // Act
        $count = $this->repository->count();

        // Assert
        self::assertSame(3, $count);
    }

    /**
     * @test
     */
    public function it_should_throw_when_trying_to_find_a_non_existing_article_id(): void
    {
        // Assert
        self::expectException(ArticleNotFoundException::class);

        // Act
        $this->repository->findOrThrow(new ArticleId('ddb01803-ef13-4195-bed1-3320a6b443ba'));
    }
}

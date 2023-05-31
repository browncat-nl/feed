<?php

namespace Unit\Feed\Application\Query\Article\Handler;

use App\Feed\Application\Query\Article\Handler\LatestUpdatedArticlesHandler;
use App\Feed\Application\Query\Article\LatestUpdatedArticlesQuery;
use App\Feed\Domain\Article\ArticleRepository;
use DateTime;
use Dev\Feed\Factory\ArticleFactory;
use Dev\Feed\Repository\InMemoryArticleRepository;
use PHPUnit\Framework\TestCase;

final class LatestUpdatedArticlesHandlerTest extends TestCase
{
    private LatestUpdatedArticlesHandler $handler;

    private ArticleRepository $articleRepository;

    public function setUp(): void
    {
        $this->articleRepository = new InMemoryArticleRepository();

        $this->handler = new LatestUpdatedArticlesHandler($this->articleRepository);
    }

    /**
     * @test
     */
    public function it_should_retrieve_last_n_articles(): void
    {
        // Arrange
        $article1 = (new ArticleFactory())->withUpdated(new DateTime('2000-05-20 8:00'))->create();
        $article2 = (new ArticleFactory())->withUpdated(new DateTime('1999-05-20 8:00'))->create();
        $article3 = (new ArticleFactory())->withUpdated(new DateTime('2000-05-20 8:01'))->create();
        $article4 = (new ArticleFactory())->withUpdated(new DateTime('1998-05-20 8:00'))->create();
        $article5 = (new ArticleFactory())->withUpdated(new DateTime('2000-06-20 8:00'))->create();
        $article6 = (new ArticleFactory())->withUpdated(new DateTime('2000-05-20 7:59'))->create();

        $this->articleRepository->save($article1, $article2, $article3, $article4, $article5, $article6);

        $query = new LatestUpdatedArticlesQuery(3);

        // Act
        $articles = $this->handler->__invoke($query);

        // Assert
        self::assertCount(3, $articles);

        self::assertSame($articles[0], $article5);
        self::assertSame($articles[1], $article3);
        self::assertSame($articles[2], $article1);
    }

    /**
     * @test
     */
    public function it_should_return_empty_array_when_there_are_no_articles(): void
    {
        // Arrange
        $query = new LatestUpdatedArticlesQuery(10);

        // Act
        $articles = $this->handler->__invoke($query);

        // Assert
        self::assertCount(0, $articles);
    }

    /**
     * @test
     */
    public function it_should_return_subset_if_asked_articles_expands_available(): void
    {
        // Arrange
        $article1 = (new ArticleFactory())->withUpdated(new DateTime('2000-05-20 8:00'))->create();
        $article2 = (new ArticleFactory())->withUpdated(new DateTime('1999-05-20 8:00'))->create();
        $article3 = (new ArticleFactory())->withUpdated(new DateTime('2000-05-20 8:01'))->create();

        $this->articleRepository->save($article1, $article2, $article3);

        $query = new LatestUpdatedArticlesQuery(100);

        // Act
        $articles = $this->handler->__invoke($query);

        // Assert
        self::assertCount(3, $articles);

        self::assertSame($articles[0], $article3);
        self::assertSame($articles[1], $article1);
        self::assertSame($articles[2], $article2);
    }
}
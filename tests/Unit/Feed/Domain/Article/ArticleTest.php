<?php

namespace Unit\Feed\Domain\Article;

use Dev\Feed\Factory\ArticleFactory;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_not_update_the_article_if_the_updates_are_older(): void
    {
        // Arrange
        $article = ArticleFactory::setup()
            ->withTitle('existing title')
            ->withSummary('existing summary')
            ->withUpdated(new \DateTime('2022-03-03 00:00:00'))
            ->create();

        // Act
        $article->updateArticle(
            'updated title',
            'updated summary',
            $article->getUrl(),
            new \DateTime('2000-01-01 00:00:00'),
            $article->getSource(),
        );

        // Assert
        self::assertEquals('existing title', $article->getTitle());
        self::assertEquals('existing summary', $article->getSummary());
        self::assertEquals(new \DateTime('2022-03-03 00:00:00'), $article->getUpdated());
    }

    /**
     * @test
     */
    public function it_should_not_update_the_article_if_the_updates_happened_at_the_same_time(): void
    {
        // Arrange
        $article = ArticleFactory::setup()
            ->withTitle('existing title')
            ->withSummary('existing summary')
            ->withUpdated(new \DateTime('2022-03-03 00:00:00'))
            ->create();

        // Act
        $article->updateArticle(
            'updated title',
            'updated summary',
            $article->getUrl(),
            new \DateTime('2022-03-03 00:00:00'),
            $article->getSource(),
        );

        // Assert
        self::assertEquals('existing title', $article->getTitle());
        self::assertEquals('existing summary', $article->getSummary());
        self::assertEquals(new \DateTime('2022-03-03 00:00:00'), $article->getUpdated());
    }

    /**
     * @test
     */
    public function it_should_update_the_article_if_the_updates_are_newer(): void
    {
        // Arrange
        $article = ArticleFactory::setup()
            ->withTitle('existing title')
            ->withSummary('existing summary')
            ->withUpdated(new \DateTime('2022-03-03 00:00:00'))
            ->create();

        // Act
        $article->updateArticle(
            'updated title',
            'updated summary',
            $article->getUrl(),
            new \DateTime('2024-10-10 10:00:00'),
            $article->getSource(),
        );

        // Assert
        self::assertEquals('updated title', $article->getTitle());
        self::assertEquals('updated summary', $article->getSummary());
        self::assertEquals(new \DateTime('2024-10-10 10:00:00'), $article->getUpdated());
    }
}

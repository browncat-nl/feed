<?php

namespace Unit\Feed\Application\Listener\Article;

use App\Feed\Application\Listener\Article\InvalidateArticleCacheListener;
use App\Feed\Domain\Article\Event\Article\ArticleUpdatedEvent;
use App\Feed\Infrastructure\Cache\FeedCacheKeys;
use Dev\Feed\Factory\ArticleFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Contracts\Cache\ItemInterface;

final class InvalidateArticleCacheListenerTest extends TestCase
{
    private InvalidateArticleCacheListener $listener;
    private ArrayAdapter $cache;

    protected function setUp(): void
    {
        $this->listener = new InvalidateArticleCacheListener(
            $this->cache = new ArrayAdapter(),
        );
    }

    /**
     * @test
     */
    public function it_should_delete_the_cache_entry_when_the_article_is_updated(): void
    {
        // Arrange
        $article = ArticleFactory::setup()->create();
        $cacheKey = sprintf(FeedCacheKeys::ARTICLE_WITH_ARTICLE_ID->value, $article->getId());

        $this->cache->get(
            $cacheKey,
            fn (ItemInterface $item) => $article,
        );

        // Act
        $this->listener->onArticleUpdated(new ArticleUpdatedEvent($article->getId()));

        // Assert
        self::assertFalse($this->cache->hasItem($cacheKey));
    }
}

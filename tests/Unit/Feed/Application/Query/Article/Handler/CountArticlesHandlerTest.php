<?php

namespace Unit\Feed\Application\Query\Article\Handler;

use App\Feed\Application\Query\Article\CountArticlesQuery;
use App\Feed\Application\Query\Article\Handler\CountArticlesHandler;
use App\Feed\Infrastructure\Cache\FeedCacheKeys;
use Dev\Common\Infrastructure\Cache\RecordingCache;
use Dev\Feed\Factory\ArticleFactory;
use Dev\Feed\Repository\InMemoryArticleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CountArticlesHandlerTest extends TestCase
{
    private CountArticlesHandler $handler;

    private InMemoryArticleRepository $repository;
    private RecordingCache $cache;

    protected function setUp(): void
    {
        $this->repository = new InMemoryArticleRepository();
        $this->cache = new RecordingCache(new ArrayAdapter());

        $this->handler = new CountArticlesHandler(
            $this->repository,
            $this->cache,
        );
    }

    /**
     * @test
     */
    public function it_should_count_the_articles(): void
    {
        // Arrange
        $this->repository->save(
            ArticleFactory::setup()->create(),
            ArticleFactory::setup()->create(),
            ArticleFactory::setup()->create(),
        );

        // Act
        $count = $this->handler->__invoke(new CountArticlesQuery());

        // Assert
        self::assertSame(3, $count);
    }

    /**
     * @test
     */
    public function it_should_get_the_count_from_the_cache(): void
    {
        // Arrange
        $this->cache->get(FeedCacheKeys::TOTAL_ARTICLES_COUNT->value, fn (ItemInterface $item) => 109);

        // Act
        $count = $this->handler->__invoke(new CountArticlesQuery());

        // Assert
        self::assertTrue($this->cache->cacheIsHitForKey(FeedCacheKeys::TOTAL_ARTICLES_COUNT->value));

        self::assertSame(109, $count);
    }
}

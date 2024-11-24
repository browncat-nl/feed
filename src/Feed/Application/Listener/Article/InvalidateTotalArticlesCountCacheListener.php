<?php

namespace App\Feed\Application\Listener\Article;

use App\Common\Infrastructure\Messenger\EventBus\AsEventSubscriber;
use App\Feed\Domain\Article\Event\Article\ArticleAddedEvent;
use App\Feed\Infrastructure\Cache\FeedCacheKeys;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class InvalidateTotalArticlesCountCacheListener
{
    public function __construct(
        private CacheInterface $cache
    ) {
    }

    #[AsEventSubscriber]
    public function onArticleAdded(ArticleAddedEvent $event): void
    {
        $this->cache->delete(FeedCacheKeys::TOTAL_ARTICLES_COUNT->value);
    }
}

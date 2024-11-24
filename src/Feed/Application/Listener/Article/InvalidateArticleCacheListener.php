<?php

namespace App\Feed\Application\Listener\Article;

use App\Common\Infrastructure\Messenger\EventBus\AsEventSubscriber;
use App\Feed\Domain\Article\Event\Article\ArticleUpdatedEvent;
use App\Feed\Infrastructure\Cache\FeedCacheKeys;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class InvalidateArticleCacheListener
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    #[AsEventSubscriber]
    public function onArticleUpdated(ArticleUpdatedEvent $event): void
    {
        $this->cache->delete(sprintf(FeedCacheKeys::ARTICLE_WITH_ARTICLE_ID->value, $event->articleId));
    }
}

<?php

namespace App\Feed\Application\Query\Article\Handler;

use App\Feed\Application\Query\Article\CountArticlesQuery;
use App\Feed\Domain\Article\ArticleRepository;
use App\Feed\Infrastructure\Cache\FeedCacheKeys;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class CountArticlesHandler
{
    public function __construct(
        private ArticleRepository $repository,
        private CacheInterface $cache,
    ) {
    }

    public function __invoke(CountArticlesQuery $query): int
    {
        return $this->cache->get(
            FeedCacheKeys::TOTAL_ARTICLES_COUNT->value,
            fn() => $this->repository->count(),
        );
    }
}

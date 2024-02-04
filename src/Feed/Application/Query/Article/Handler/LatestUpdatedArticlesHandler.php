<?php

namespace App\Feed\Application\Query\Article\Handler;

use App\Feed\Application\Model\Article\ArticleReadModel;
use App\Feed\Application\Query\Article\LatestUpdatedArticlesQuery;
use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleRepository;
use App\Feed\Infrastructure\Cache\FeedCacheKeys;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class LatestUpdatedArticlesHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @return list<ArticleReadModel>
     */
    public function __invoke(LatestUpdatedArticlesQuery $query): array
    {
        $articleIds = $this->articleRepository->findLatestIds($query->offset, $query->numberOfArticles);

        $articles = [];

        foreach ($articleIds as $articleId) {
            $articles[] = $this->cache->get(
                sprintf(FeedCacheKeys::ARTICLE_WITH_ARTICLE_ID->value, $articleId),
                fn() => ArticleReadModel::fromArticle(
                    $this->articleRepository->findOrThrow($articleId)
                ),
            );
        }

        return $articles;
    }
}

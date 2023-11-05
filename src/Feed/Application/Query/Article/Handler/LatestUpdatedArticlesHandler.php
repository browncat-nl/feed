<?php

namespace App\Feed\Application\Query\Article\Handler;

use App\Feed\Application\Query\Article\LatestUpdatedArticlesQuery;
use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleRepository;

final readonly class LatestUpdatedArticlesHandler
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
    }

    /**
     * @return list<Article>
     */
    public function __invoke(LatestUpdatedArticlesQuery $query): array
    {
        return $this->articleRepository->findLatest($query->offset, $query->numberOfArticles);
    }
}

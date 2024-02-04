<?php

namespace App\Feed\Application\Query\Article\Handler;

use App\Feed\Application\Model\Article\ArticleReadModel;
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
     * @return list<ArticleReadModel>
     */
    public function __invoke(LatestUpdatedArticlesQuery $query): array
    {
        return array_map(
            ArticleReadModel::fromArticle(...),
            $this->articleRepository->findLatest($query->offset, $query->numberOfArticles),
        );
    }
}

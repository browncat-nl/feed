<?php

namespace App\Feed\Application\Query\Article;

use App\Feed\Application\Query\Article\Handler\LatestUpdatedArticlesHandler;

/**
 * @see LatestUpdatedArticlesHandler
 */
final readonly class LatestUpdatedArticlesQuery
{
    public function __construct(
        public int $offset,
        public int $numberOfArticles,
    ) {
    }
}

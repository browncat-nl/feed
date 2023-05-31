<?php

namespace App\Feed\Application\Query\Article;

final readonly class LatestUpdatedArticlesQuery
{
    public function __construct(public int $numberOfArticles)
    {
    }
}

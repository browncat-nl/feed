<?php

namespace App\Feed\Application\Query\Article\Handler;

use App\Feed\Application\Query\Article\CountArticlesQuery;
use App\Feed\Domain\Article\ArticleRepository;

final readonly class CountArticlesHandler
{
    public function __construct(
        private ArticleRepository $repository
    ) {
    }

    public function __invoke(CountArticlesQuery $query): int
    {
        return $this->repository->count();
    }
}

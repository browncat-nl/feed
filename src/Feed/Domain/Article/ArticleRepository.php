<?php

namespace App\Feed\Domain\Article;

use App\Feed\Domain\Article\Exception\ArticleNotFoundException;
use App\Feed\Domain\Article\Url\Url;

interface ArticleRepository
{
    public function save(Article ...$articles): void;

    public function find(ArticleId $id): ?Article;

    /**
     * @throws ArticleNotFoundException
     */
    public function findOrThrow(ArticleId $id): Article;

    public function count(): int;

    /**
     * @param int $numberOfArticles
     * @return list<ArticleId>
     */
    public function findLatestIds(
        int $offset,
        int $numberOfArticles,
    ): array;

    public function findByUrl(string $url): ?Article;
}

<?php

namespace App\Feed\Domain\Article;

use App\Feed\Domain\Article\Url\Url;

interface ArticleRepository
{
    public function save(Article ...$articles): void;

    public function find(ArticleId $id): ?Article;

    public function count(): int;

    /**
     * @param int $numberOfArticles
     * @return list<Article>
     */
    public function findLatest(
        int $offset,
        int $numberOfArticles,
    ): array;

    public function findByUrl(string $url): ?Article;
}

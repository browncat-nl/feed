<?php

namespace App\Feed\Domain\Article;

interface ArticleRepository
{
    public function save(Article ...$articles): void;

    public function find(ArticleId $id): ?Article;

    /**
     * @param int $numberOfArticles
     * @return list<Article>
     */
    public function findLatest(int $numberOfArticles): array;
}

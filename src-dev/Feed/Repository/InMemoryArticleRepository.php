<?php

namespace Dev\Feed\Repository;

use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\ArticleRepository;

final class InMemoryArticleRepository implements ArticleRepository
{
    /**
     * @var array<string, Article>
     */
    private array $entities;

    public function save(Article ...$articles): void
    {
        foreach ($articles as $article) {
            $this->entities[(string) $article->getId()] = $article;
        }
    }

    public function find(ArticleId $id): ?Article
    {
        return $this->entities[(string) $id] ?? null;
    }
}

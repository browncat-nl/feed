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
    private array $entities = [];

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

    public function findLatest(int $numberOfArticles): array
    {
        $entities = $this->entities;

        usort($entities, fn (Article $a, Article $b) => $a->getUpdated() > $b->getUpdated() ? -1 : 1);

        return array_values(array_slice($entities, 0, $numberOfArticles));
    }
}

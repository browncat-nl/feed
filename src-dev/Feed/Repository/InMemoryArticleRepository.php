<?php

namespace Dev\Feed\Repository;

use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\ArticleRepository;
use App\Feed\Domain\Article\Exception\ArticleNotFoundException;
use App\Feed\Domain\Article\Url\Url;

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


    public function findOrThrow(ArticleId $id): Article
    {
        return $this->find($id) ?? throw ArticleNotFoundException::withArticleId($id);
    }

    public function count(): int
    {
        return count($this->entities);
    }

    public function findLatestIds(int $offset, int $numberOfArticles): array
    {
        $entities = $this->entities;

        usort($entities, fn (Article $a, Article $b) => $a->getUpdated() > $b->getUpdated() ? -1 : 1);

        return array_map(
            fn(Article $entity) => $entity->getId(),
            array_values(array_slice($entities, $offset, $numberOfArticles))
        );
    }

    public function findByUrl(string $url): ?Article
    {
        foreach ($this->entities as $entity) {
            if ($url !== (string) $entity->getUrl()) {
                continue;
            }

            return $entity;
        }

        return null;
    }
}

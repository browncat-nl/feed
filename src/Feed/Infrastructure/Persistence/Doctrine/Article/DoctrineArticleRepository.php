<?php

namespace App\Feed\Infrastructure\Persistence\Doctrine\Article;

use App\Common\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends DoctrineRepository<Article>
 */
final readonly class DoctrineArticleRepository extends DoctrineRepository implements ArticleRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article ...$articles): void
    {
        $this->saveWithoutTransaction(...$articles);
    }

    public function find(ArticleId $id): ?Article
    {
        return $this->findWithoutLocking($id);
    }
}

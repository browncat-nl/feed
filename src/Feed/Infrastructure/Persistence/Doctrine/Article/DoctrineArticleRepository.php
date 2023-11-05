<?php

namespace App\Feed\Infrastructure\Persistence\Doctrine\Article;

use App\Common\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\ArticleRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

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

    public function count(): int
    {
        $count = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        Assert::integer($count);

        return $count;
    }

    /**
     * @param int $numberOfArticles
     * @return List<Article>
     */
    public function findLatest(
        int $offset,
        int $numberOfArticles,
    ): array {
        return $this->createQueryBuilder('a')
            ->orderBy('a.updated', Criteria::DESC)
            ->setFirstResult($offset)
            ->setMaxResults($numberOfArticles)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUrl(string $url): ?Article
    {
        return $this->createQueryBuilder('article')
            ->where('article.url = :url')
            ->setParameter('url', $url)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

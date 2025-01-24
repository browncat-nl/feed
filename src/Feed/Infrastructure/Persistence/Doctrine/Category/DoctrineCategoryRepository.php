<?php

namespace App\Feed\Infrastructure\Persistence\Doctrine\Category;

use App\Common\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\Feed\Domain\Category\Category;
use App\Feed\Domain\Category\CategoryRepository;
use App\Feed\Domain\Category\Exception\CategoryNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends DoctrineRepository<Category>
 */
final readonly class DoctrineCategoryRepository extends DoctrineRepository implements CategoryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category ...$categories): void
    {
        $this->saveWithoutTransaction(...$categories);
    }

    public function findByNameOrThrow(string $name): Category
    {
        return $this->createQueryBuilder('category')
            ->where('category.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() ?? throw CategoryNotFoundException::withName($name);
    }
}

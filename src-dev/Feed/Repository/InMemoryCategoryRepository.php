<?php

namespace Dev\Feed\Repository;

use App\Feed\Domain\Category\Category;
use App\Feed\Domain\Category\CategoryRepository;
use App\Feed\Domain\Category\Exception\CategoryNotFoundException;

class InMemoryCategoryRepository implements CategoryRepository
{
    /**
     * @var array<string, Category>
     */
    private array $entities = [];

    public function save(Category ...$categories): void
    {
        foreach ($categories as $category) {
            $this->entities[(string) $category->getId()] = $category;
        }
    }

    public function findByNameOrThrow(string $name): Category
    {
        foreach ($this->entities as $entity) {
            if ($entity->getName() != $name) {
                continue;
            }

            return $entity;
        }

        throw CategoryNotFoundException::withName($name);
    }
}

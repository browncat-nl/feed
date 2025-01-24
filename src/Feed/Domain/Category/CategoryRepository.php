<?php

namespace App\Feed\Domain\Category;

use App\Feed\Domain\Category\Exception\CategoryNotFoundException;

interface CategoryRepository
{
    public function save(Category ...$categories): void;

    /**
     * @throws CategoryNotFoundException
     */
    public function findByNameOrThrow(string $name): Category;
}

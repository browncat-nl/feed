<?php

namespace Dev\Feed\Factory;

use App\Common\Domain\Url\Url;
use App\Feed\Domain\Category\Category;
use App\Feed\Domain\Category\CategoryId;
use App\Feed\Domain\Source\SourceId;
use Ramsey\Uuid\Uuid;

class CategoryFactory
{
    private CategoryId $id;
    private string $name;

    public function __construct()
    {
        $faker = \Faker\Factory::create();

        $this->id = new CategoryId(Uuid::uuid4());
        $this->name = $faker->colorName();
    }

    public static function setup(): self
    {
        return new self();
    }

    public function create(): Category
    {
        return new Category(
            $this->id,
            $this->name,
        );
    }
}

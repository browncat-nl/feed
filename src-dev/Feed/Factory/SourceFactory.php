<?php

namespace Dev\Feed\Factory;

use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
use Ramsey\Uuid\Uuid;

class SourceFactory
{
    private SourceId $id;
    private string $name;

    public function __construct()
    {
        $faker = \Faker\Factory::create();

        $this->id = new SourceId(Uuid::uuid4());
        $this->name = $faker->company();
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function create(): Source
    {
        return new Source(
            $this->id,
            $this->name,
        );
    }
}

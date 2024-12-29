<?php

namespace Dev\Feed\Factory;

use App\Common\Domain\Url\Url;
use App\Feed\Domain\Source\Source;
use App\Feed\Domain\Source\SourceId;
use Ramsey\Uuid\Uuid;

class SourceFactory
{
    private SourceId $id;
    private string $name;
    private Url $url;

    public function __construct()
    {
        $faker = \Faker\Factory::create();

        $this->id = new SourceId(Uuid::uuid4());
        $this->name = $faker->company();
        $this->url = Url::createFromString($faker->url());
    }

    public static function setup(): self
    {
        return new self();
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withUrl(string|Url $url): self
    {
        $this->url = $url instanceof Url ? $url : Url::createFromString($url);

        return $this;
    }

    public function create(): Source
    {
        return new Source(
            $this->id,
            $this->name,
            $this->url,
        );
    }
}

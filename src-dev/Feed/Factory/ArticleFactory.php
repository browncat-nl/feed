<?php

namespace Dev\Feed\Factory;

use App\Feed\Domain\Article\Article;
use App\Feed\Domain\Article\ArticleId;
use App\Feed\Domain\Article\Url\Url;
use App\Feed\Domain\Source\Source;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

final class ArticleFactory
{
    private ArticleId $id;
    private string $title;
    private string $summary;
    private Url $url;
    private DateTime $updated;
    private Source $source;

    private function __construct(private readonly ?EntityManagerInterface $entityManager)
    {
        $faker = \Faker\Factory::create();

        $this->id = new ArticleId(Uuid::uuid4());
        $this->title = $faker->sentence();
        $this->summary = $faker->paragraph();
        $this->url = Url::createFromString($faker->url());
        $this->updated = $faker->dateTime();
        $this->source = SourceFactory::setup()->create();
    }

    public static function setup(?EntityManagerInterface $entityManager = null): self
    {
        return new self($entityManager);
    }

    public function withSource(Source $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function withUpdated(DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function create(): Article
    {
        $article = new Article(
            $this->id,
            $this->title,
            $this->summary,
            $this->url,
            $this->updated,
            $this->source,
        );

        if ($this->entityManager == null) {
            return $article;
        }

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }
}

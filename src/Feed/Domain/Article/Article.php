<?php

namespace App\Feed\Domain\Article;

use App\Feed\Domain\Article\Url\Url;
use App\Feed\Domain\Source\Source;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
final readonly class Article
{
    #[ORM\Id, ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $summary;

    #[ORM\Column(type: 'text')]
    private string $url;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updated;

    #[ORM\ManyToOne(targetEntity: Source::class)]
    private Source $source;

    public function __construct(
        ArticleId $id,
        string $title,
        string $summary,
        Url $url,
        DateTime $updated,
        Source $source,
    ) {
        $this->id = (string) $id;
        $this->title = $title;
        $this->summary = $summary;
        $this->url = (string) $url;
        $this->updated = $updated;
        $this->source = $source;
    }

    public function getId(): ArticleId
    {
        return new ArticleId($this->id);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getUrl(): Url
    {
        return Url::createFromString($this->url);
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function getSource(): Source
    {
        return $this->source;
    }
}

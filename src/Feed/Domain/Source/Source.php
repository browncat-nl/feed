<?php

namespace App\Feed\Domain\Source;

use App\Common\Domain\Url\Url;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sources')]
#[ORM\Index(columns: ['name'], name: 'name_index')]
class Source
{
    #[ORM\Id, ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column]
    private string $name;

    #[ORM\Column(type: 'string', length: 512)]
    private string $feedUrl;

    public function __construct(
        SourceId $id,
        string $name,
        Url $feedUrl,
    ) {
        $this->id = (string) $id;
        $this->name = $name;
        $this->feedUrl = (string) $feedUrl;
    }

    public function getId(): SourceId
    {
        return new SourceId($this->id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): Url
    {
        return Url::createFromString($this->feedUrl);
    }
}

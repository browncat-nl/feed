<?php

namespace App\Feed\Domain\Source;

use App\Feed\Infrastructure\Persistence\Doctrine\Source\DoctrineSourceRepository;
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

    public function __construct(
        SourceId $id,
        string $name,
    ) {
        $this->id = (string) $id;
        $this->name = $name;
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
}

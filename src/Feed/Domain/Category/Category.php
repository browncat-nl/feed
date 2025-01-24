<?php

namespace App\Feed\Domain\Category;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
#[ORM\Index(name: 'category_name_index', columns: ['name'])]
class Category
{
    #[ORM\Id, ORM\Column(type: 'string')]
    private string $id;
    #[ORM\Column]
    private string $name;

    public function __construct(
        CategoryId $id,
        string $name,
    ) {
        $this->id = (string) $id;
        $this->name = $name;
    }

    public function getId(): CategoryId
    {
        return new CategoryId($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

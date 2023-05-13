<?php

namespace App\Feed\Domain\Article;

use Stringable;

final readonly class ArticleId implements Stringable
{
    public function __construct(private string $id)
    {
    }

    public function __toString(): string
    {
        return $this->id;
    }
}

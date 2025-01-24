<?php

namespace App\Feed\Domain\Category\Exception;

class CategoryNotFoundException extends \Exception
{
    public static function withName(string $name): self
    {
        return new self(
            sprintf('Category with name %s not found.', $name)
        );
    }
}

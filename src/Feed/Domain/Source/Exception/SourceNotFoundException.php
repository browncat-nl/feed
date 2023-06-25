<?php

namespace App\Feed\Domain\Source\Exception;

use App\Feed\Domain\Source\SourceId;
use Exception;

final class SourceNotFoundException extends Exception
{
    public static function withSourceId(SourceId $sourceId): self
    {
        return new self(
            sprintf('Source with id %s not found.', $sourceId)
        );
    }

    public static function withName(string $name): self
    {
        return new self(
            sprintf('Source with name %s not found.', $name)
        );
    }
}

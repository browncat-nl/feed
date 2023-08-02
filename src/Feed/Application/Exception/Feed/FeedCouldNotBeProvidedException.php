<?php

namespace App\Feed\Application\Exception\Feed;

use Exception;

final class FeedCouldNotBeProvidedException extends Exception
{
    public static function withNonExistingSource(string $source): self
    {
        return new self(sprintf('There is no feed provider coupled to the `%s` source', $source));
    }
}

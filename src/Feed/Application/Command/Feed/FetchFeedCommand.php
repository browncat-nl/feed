<?php

namespace App\Feed\Application\Command\Feed;

use App\Feed\Application\Command\Feed\Handler\FetchFeedHandler;

/**
 * @see FetchFeedHandler
 */
final readonly class FetchFeedCommand
{
    /**
     * @param string $source
     */
    public function __construct(
        public string $source
    ) {
    }
}

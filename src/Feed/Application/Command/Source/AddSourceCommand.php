<?php

namespace App\Feed\Application\Command\Source;

use App\Feed\Application\Command\Source\Handler\AddSourceHandler;

/**
 * @see AddSourceHandler
 */
final readonly class AddSourceCommand
{
    public function __construct(
        public string $sourceId,
        public string $name,
        public string $feedUrl,
        public string $category,
    ) {
    }
}

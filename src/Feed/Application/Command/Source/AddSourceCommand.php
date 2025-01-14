<?php

namespace App\Feed\Application\Command\Source;

final readonly class AddSourceCommand
{
    public function __construct(
        public string $sourceId,
        public string $name,
        public string $feedUrl,
    ) {
    }
}

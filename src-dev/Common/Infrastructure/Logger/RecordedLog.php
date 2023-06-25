<?php

namespace Dev\Common\Infrastructure\Logger;

use DateTimeImmutable;

final readonly class RecordedLog
{
    /**
     * @param array<mixed, mixed> $context
     */
    public function __construct(
        public string $message,
        public array $context,
        public string $level,
        public DateTimeImmutable $created,
    ) {
    }
}

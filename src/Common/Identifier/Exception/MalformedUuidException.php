<?php

namespace App\Common\Identifier\Exception;

use Exception;

final class MalformedUuidException extends Exception
{
    private function __construct(
        string $message,
        public string $uuid,
    ) {
        parent::__construct($message);
    }

    public static function withUuid(string $uuid): self
    {
        return new self(
            sprintf('%s is not a valid uuid.', $uuid),
            $uuid
        );
    }
}

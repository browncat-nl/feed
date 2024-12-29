<?php

namespace App\Common\Domain\Url\Exception;

use Exception;

final class SchemeNotSupportedException extends Exception
{
    public static function withScheme(string $scheme): self
    {
        return new self(
            sprintf('%s is not in the list of supported schemes.', $scheme),
        );
    }
}

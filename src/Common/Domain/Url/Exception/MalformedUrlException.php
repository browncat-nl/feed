<?php

namespace App\Common\Domain\Url\Exception;

use Exception;

final class MalformedUrlException extends Exception
{
    public static function withUrl(string $url): self
    {
        return new self(
            sprintf('Url %s is not valid.', $url),
        );
    }
}

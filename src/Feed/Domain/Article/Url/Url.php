<?php

namespace App\Feed\Domain\Article\Url;

use App\Feed\Domain\Article\Url\Exception\MalformedUrlException;
use App\Feed\Domain\Article\Url\Exception\SchemeNotSupportedException;

final readonly class Url
{
    private function __construct(
        public Scheme $scheme,
        public string $hostname,
        public ?string $path,
        public ?string $query,
        public ?string $fragment,
    ) {
    }

    /**
     * @throws MalformedUrlException
     */
    public static function createFromString(string $url): self
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw MalformedUrlException::withUrl($url);
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        $fragment = parse_url($url, PHP_URL_FRAGMENT);

        // This could have been an in_array(false, [..]) but the AST parser does not like that.
        if ($scheme === false || $host === false || $path === false || $query === false || $fragment === false) {
            throw MalformedUrlException::withUrl($url);
        }

        return new self(
            Scheme::tryFrom((string) $scheme) ?? throw SchemeNotSupportedException::withScheme((string) $scheme),
            (string) $host,
            $path,
            $query,
            $fragment,
        );
    }

    public function __toString(): string
    {
        $url = sprintf('%s://%s', $this->scheme->value, $this->hostname);

        if ($this->path !== null) {
            $url .= $this->path;
        }

        if ($this->query !== null) {
            $url .= '?' . $this->query;
        }

        if ($this->fragment !== null) {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }
}

<?php

namespace Unit\Feed\Domain\Article\Url;

use App\Common\Domain\Url\Exception\MalformedUrlException;
use App\Common\Domain\Url\Exception\SchemeNotSupportedException;
use App\Common\Domain\Url\Scheme;
use App\Common\Domain\Url\Url;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    /**
     * @test
     * @dataProvider supportedUrlProvider
     *
     * @param array<string, string|null> $expectedPieces
     */
    public function it_should_create_url_from_string(string $urlString, array $expectedPieces): void
    {
        // Act
        $url = Url::createFromString($urlString);

        // Assert
        self::assertSame($urlString, (string) $url);

        self::assertSame($url->scheme, $expectedPieces['scheme']);
        self::assertSame($url->hostname, $expectedPieces['hostname']);
        self::assertSame($url->path, $expectedPieces['path']);
        self::assertSame($url->query, $expectedPieces['query']);
        self::assertSame($url->fragment, $expectedPieces['fragment']);
    }

    /**
     * @test
     */
    public function it_should_guard_if_the_scheme_is_not_supported(): void
    {
        // Arrange
        $url = 'ftp://example.com';

        // Assert
        self::expectExceptionObject(SchemeNotSupportedException::withScheme('ftp'));

        // Act
        Url::createFromString($url);
    }

    /**
     * @test
     * @dataProvider malformedUrlProvider
     */
    public function it_should_guard_if_url_is_malformed(string $urlString): void
    {
        // Assert
        self::expectExceptionObject(MalformedUrlException::withUrl($urlString));

        // Act
        Url::createFromString($urlString);
    }

    /**
     * @return array<string, array<string|array<string,mixed>>>
     */
    public static function supportedUrlProvider(): array
    {
        return [
            'basic' => [
                'http://example.com/example-path',
                [
                    'scheme' => Scheme::HTTP,
                    'hostname' => 'example.com',
                    'path' => '/example-path',
                    'query' => null,
                    'fragment' => null,
                ]
            ],
            'without path' => [
                'https://example.com',
                [
                    'scheme' => Scheme::HTTPS,
                    'hostname' => 'example.com',
                    'path' => null,
                    'query' => null,
                    'fragment' => null,
                ]
            ],
            'rich' => [
                'https://example.com/example-path?test=2&data=string#header-2',
                [
                    'scheme' => Scheme::HTTPS,
                    'hostname' => 'example.com',
                    'path' => '/example-path',
                    'query' => 'test=2&data=string',
                    'fragment' => 'header-2',
                ]
            ],
            'without tld' => [
                'http://example',
                [
                    'scheme' => Scheme::HTTP,
                    'hostname' => 'example',
                    'path' => null,
                    'query' => null,
                    'fragment' => null,
                ]
            ]
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function malformedUrlProvider(): array
    {
        return [
            ['example.com/test'],
            ['http:///test'],
            ['http://example.com&data=2'],
        ];
    }
}

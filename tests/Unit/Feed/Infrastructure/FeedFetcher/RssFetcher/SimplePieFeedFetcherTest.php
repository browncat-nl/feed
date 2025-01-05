<?php

namespace Unit\Feed\Infrastructure\FeedFetcher\RssFetcher;

use App\Feed\Infrastructure\FeedFetcher\RssFetcher\SimplePieFeedFetcher;
use Dev\Common\Infrastructure\Logger\InMemoryLogger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class SimplePieFeedFetcherTest extends TestCase
{
    private const EXTERNAL_FEED = <<<XML
<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom">
    <title>PHP.Watch: PHP Version Changes</title>
    <id>https://php.watch/versions</id>
    <link href="https://php.watch/versions"/>
    <updated>2023-10-17T10:44:00+00:00</updated>
    <author><name>Ayesh Karunaratne</name></author>
        <subtitle>Recent changes in PHP language.</subtitle>
        <entry>
            <title>Password Hashing: Default Bcrypt cost changed from `10` to `12`</title>
            <link href="https://php.watch/versions/8.4/password_hash-bcrypt-cost-increase"/>
            <id>https://php.watch/versions/8.4/password_hash-bcrypt-cost-increase</id>
            <updated>2023-10-17T10:44:00+00:00</updated>
            <summary>Change type: Change

		Target version: 8.4
</summary>
        </entry>
        <entry>
            <title>phpinfo: Show PHP Integer Size information</title>
            <link href="https://php.watch/versions/8.4/phpinfo-int-size"/>
            <id>https://php.watch/versions/8.4/phpinfo-int-size</id>
            <updated>2023-09-23T10:44:00+00:00</updated>
            <summary>Change type: New Feature

		Target version: 8.4
</summary>
        </entry>
        <entry>
            <title>Class constant type declarations in some PHP extension classes</title>
            <link href="https://php.watch/versions/8.3/ext-class-constant-type-declarations"/>
            <id>https://php.watch/versions/8.3/ext-class-constant-type-declarations</id>
            <updated>2023-08-22T10:44:00+00:00</updated>
            <summary>Change type: Change

		Target version: 8.3
</summary>
        </entry>
</feed>
XML;

    private const EXTERNAL_FEED_WITH_MALFORMED_ENTRY = <<<XML
<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom">
    <title>PHP.Watch: PHP Version Changes</title>
    <id>https://php.watch/versions</id>
    <link href="https://php.watch/versions"/>
    <updated>2023-10-17T10:44:00+00:00</updated>
    <author><name>Ayesh Karunaratne</name></author>
        <subtitle>Recent changes in PHP language.</subtitle>
        <entry>
            <title>Entry with missing publish date</title>
            <link href="https://php.watch/versions/8.4/password_hash-bcrypt-cost-increase"/>
            <id>https://php.watch/versions/8.4/password_hash-bcrypt-cost-increase</id>
        </entry>
        <entry>
            <title>Class constant type declarations in some PHP extension classes</title>
            <link href="https://php.watch/versions/8.3/ext-class-constant-type-declarations"/>
            <id>https://php.watch/versions/8.3/ext-class-constant-type-declarations</id>
            <updated>2023-08-22T10:44:00+00:00</updated>
            <summary>Change type: Change

		Target version: 8.3
</summary>
        </entry>
</feed>
XML;

    /**
     * @test
     */
    public function it_should_fetch_and_parse_the_feed(): void
    {
        // Arrange
        $client = new MockHttpClient([
            new MockResponse(self::EXTERNAL_FEED, ['response_headers' => ['Content-Type' => 'application/rss+xml']])
        ]);

        $feedFetcher = new SimplePieFeedFetcher($client, new InMemoryLogger());

        // Act
        $feedItems = $feedFetcher->__invoke('some-source', 'https://example.com/rss');

        // Assert
        self::assertCount(3, $feedItems);

        self::assertSame("Password Hashing: Default Bcrypt cost changed from `10` to `12`", $feedItems[0]->title);
        self::assertSame("Change type: Change    Target version: 8.4", $feedItems[0]->summary);
        self::assertSame("https://php.watch/versions/8.4/password_hash-bcrypt-cost-increase", $feedItems[0]->url);
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-10-17 10:44'), $feedItems[0]->updated);
        self::assertSame('some-source', $feedItems[0]->source);

        self::assertSame("phpinfo: Show PHP Integer Size information", $feedItems[1]->title);
        self::assertSame("Change type: New Feature    Target version: 8.4", $feedItems[1]->summary);
        self::assertSame("https://php.watch/versions/8.4/phpinfo-int-size", $feedItems[1]->url);
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-09-23 10:44'), $feedItems[1]->updated);
        self::assertSame('some-source', $feedItems[1]->source);

        self::assertSame("Class constant type declarations in some PHP extension classes", $feedItems[2]->title);
        self::assertSame("Change type: Change    Target version: 8.3", $feedItems[2]->summary);
        self::assertSame("https://php.watch/versions/8.3/ext-class-constant-type-declarations", $feedItems[2]->url);
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-08-22 10:44'), $feedItems[2]->updated);
        self::assertSame('some-source', $feedItems[2]->source);
    }

    /**
     * @test
     */
    public function it_should_skip_and_log_a_malformed_entry(): void
    {
        // Arrange
        $client = new MockHttpClient([
            new MockResponse(self::EXTERNAL_FEED_WITH_MALFORMED_ENTRY, ['response_headers' => ['Content-Type' => 'application/rss+xml']])
        ]);
        $logger = new InMemoryLogger();

        $feedFetcher = new SimplePieFeedFetcher($client, $logger);

        // Act
        $feedItems = $feedFetcher->__invoke('some-source', 'https://example.com/rss');

        // Assert
        self::assertCount(1, $feedItems);

        self::assertCount(1, $logger->recordedLogs);

        $log = $logger->recordedLogs[0];
        self::assertSame('[SimplePieParser] Could not parse entry', $log->message);
        self::assertSame([
            'source' => 'some-source',
            'feed_url' => 'https://example.com/rss',
            'id' => 'https://php.watch/versions/8.4/password_hash-bcrypt-cost-increase',
        ], $log->context);
    }
}

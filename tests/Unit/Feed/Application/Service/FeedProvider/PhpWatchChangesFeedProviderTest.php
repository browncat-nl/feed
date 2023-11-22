<?php

namespace Unit\Feed\Application\Service\FeedProvider;

use App\Feed\Application\Service\FeedProvider\PhpWatchChangesFeedProvider;
use Dev\Common\Infrastructure\Logger\InMemoryLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class PhpWatchChangesFeedProviderTest extends TestCase
{
    private InMemoryLogger $logger;

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

    private const MALFORMED_EXTERNAL_FEED = <<<XML
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
            <summary>Change type: Change

		Target version: 8.4
</summary>
        </entry>
</feed>
XML;

    protected function setUp(): void
    {
        $this->logger = new InMemoryLogger();

        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_fetch_the_external_feed(): void
    {
        // Arrange
        $client = new MockHttpClient([
            new MockResponse(self::EXTERNAL_FEED)
        ]);

        $feedProvider = new PhpWatchChangesFeedProvider($client, $this->logger);

        // Act
        $feedItems = $feedProvider->fetchFeedItems();

        // Assert
        self::assertCount(3, $feedItems);

        self::assertSame("Password Hashing: Default Bcrypt cost changed from `10` to `12`", $feedItems[0]->title);
        self::assertSame("Change type: Change    Target version: 8.4 ", $feedItems[0]->summary);
        self::assertSame("https://php.watch/versions/8.4/password_hash-bcrypt-cost-increase", $feedItems[0]->url);
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-10-17 10:44'), $feedItems[0]->updated);
        self::assertSame('php.watch', $feedItems[0]->source);

        self::assertSame("phpinfo: Show PHP Integer Size information", $feedItems[1]->title);
        self::assertSame("Change type: New Feature    Target version: 8.4 ", $feedItems[1]->summary);
        self::assertSame("https://php.watch/versions/8.4/phpinfo-int-size", $feedItems[1]->url);
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-09-23 10:44'), $feedItems[1]->updated);
        self::assertSame('php.watch', $feedItems[1]->source);

        self::assertSame("Class constant type declarations in some PHP extension classes", $feedItems[2]->title);
        self::assertSame("Change type: Change    Target version: 8.3 ", $feedItems[2]->summary);
        self::assertSame("https://php.watch/versions/8.3/ext-class-constant-type-declarations", $feedItems[2]->url);
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-08-22 10:44'), $feedItems[2]->updated);
        self::assertSame('php.watch', $feedItems[2]->source);
    }

    /**
     * @test
     */
    public function it_should_get_the_source_name(): void
    {
        // Assert
        self::assertSame('php.watch', PhpWatchChangesFeedProvider::getSource());
    }

    /**
     * @test
     */
    public function it_should_log_if_entry_cant_be_parsed(): void
    {
        // Arrange
        $client = new MockHttpClient([
            new MockResponse(self::MALFORMED_EXTERNAL_FEED)
        ]);

        $feedProvider = new PhpWatchChangesFeedProvider($client, $this->logger);

        // Act
        $feedItems = $feedProvider->fetchFeedItems();

        // Assert
        self::assertCount(0, $feedItems);

        self::assertCount(1, $this->logger->recordedLogs);

        $log = $this->logger->recordedLogs[0];

        self::assertSame(LogLevel::WARNING, $log->level);
        self::assertSame('[{source}] Failed to parse entry: {reason}.', $log->message);
        self::assertSame([
            'source' => PhpWatchChangesFeedProvider::getSource(),
            'reason' => "`updated` does not exist in DOMElement",
        ], $log->context);
    }
}

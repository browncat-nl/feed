<?php

namespace Unit\Feed\Application\Service\FeedProvider;

use App\Feed\Application\Service\FeedProvider\PhpWatchChangesFeedProvider;
use App\Feed\Application\Service\FeedProvider\PhpWatchNewsFeedProvider;
use Dev\Common\Infrastructure\Logger\InMemoryLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class PhpWatchNewsFeedProviderTest extends TestCase
{
    private InMemoryLogger $logger;

    private const EXTERNAL_FEED = <<<XML
<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom">
    <title>PHP.Watch News</title>
    <id>https://php.watch/news</id>
    <link href="https://php.watch/news"/>
    <updated>2023-08-31T10:44:00+00:00</updated>
    <author><name>Ayesh Karunaratne</name></author>
        <entry>
            <title>First PHP 8.3 Release Candidate is now available for testing</title>
            <link href="https://php.watch/news/2023/08/php83-rc1-released"/>
            <id>https://php.watch/news/2023/08/php83-rc1-released</id>
            <updated>2023-08-31T10:44:00+00:00</updated>
            <summary>The first release candidate (RC1) for PHP 8.3 is now released, along with Windows QA builds and Docker images.</summary>
        </entry>
        <entry>
            <title>PHP 8.3 Beta Released</title>
            <link href="https://php.watch/news/2023/07/php83-beta-released"/>
            <id>https://php.watch/news/2023/07/php83-beta-released</id>
            <updated>2023-07-24T10:44:00+00:00</updated>
            <summary>The first beta release of the upcoming PHP 8.3 is released.</summary>
        </entry>
</feed>
XML;

    private const MALFORMED_EXTERNAL_FEED = <<<XML
<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom">
    <title>PHP.Watch News</title>
    <id>https://php.watch/news</id>
    <link href="https://php.watch/news"/>
    <updated>2023-08-31T10:44:00+00:00</updated>
    <author><name>Ayesh Karunaratne</name></author>
        <entry>
            <title>First PHP 8.3 Release Candidate is now available for testing</title>
            <link href="https://php.watch/news/2023/08/php83-rc1-released"/>
            <id>https://php.watch/news/2023/08/php83-rc1-released</id>
            <summary>The first release candidate (RC1) for PHP 8.3 is now released, along with Windows QA builds and Docker images.</summary>
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

        $feedProvider = new PhpWatchNewsFeedProvider($client, $this->logger);

        // Act
        $feedItems = $feedProvider->fetchFeedItems();

        // Assert
        self::assertCount(2, $feedItems);

        self::assertSame("First PHP 8.3 Release Candidate is now available for testing", $feedItems[0]->title);
        self::assertSame("The first release candidate (RC1) for PHP 8.3 is now released, along with Windows QA builds and Docker images.", $feedItems[0]->summary);
        self::assertSame("https://php.watch/news/2023/08/php83-rc1-released", $feedItems[0]->url);
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-08-31 10:44'), $feedItems[0]->updated);
        self::assertSame('php.watch', $feedItems[0]->source);

        self::assertSame("PHP 8.3 Beta Released", $feedItems[1]->title);
        self::assertSame("The first beta release of the upcoming PHP 8.3 is released.", $feedItems[1]->summary);
        self::assertSame("https://php.watch/news/2023/07/php83-beta-released", $feedItems[1]->url);
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-07-24 10:44'), $feedItems[1]->updated);
        self::assertSame('php.watch', $feedItems[1]->source);
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

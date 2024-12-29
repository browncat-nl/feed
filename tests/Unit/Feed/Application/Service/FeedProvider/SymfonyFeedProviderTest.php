<?php

namespace Unit\Feed\Application\Service\FeedProvider;

use App\Feed\Application\Service\FeedProvider\SymfonyFeedProvider;
use App\Feed\Infrastructure\FeedParser\RssParser\SimplePieFeedParser;
use Dev\Common\Infrastructure\Logger\InMemoryLogger;
use Dev\Feed\Factory\SourceFactory;
use Dev\Feed\Repository\InMemorySourceRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class SymfonyFeedProviderTest extends TestCase
{
    private InMemoryLogger $logger;
    private InMemorySourceRepository $sourceRepository;

    private const EXTERNAL_FEED = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
        <title>Symfony Blog</title>
        <atom:link href="https://feeds.feedburner.com/symfony/blog" rel="self" type="application/rss+xml" />
        <link>https://symfony.com/blog/</link>
        <description>Most recent posts published on the Symfony project blog</description>
        <pubDate>Sun, 19 Nov 2023 21:44:09 +0100</pubDate>
        <lastBuildDate>Sun, 19 Nov 2023 08:58:00 +0100</lastBuildDate>
        <language>en</language>
        <item>
            <title><![CDATA[SymfonyCon Brussels 2023: A Memorable Game UX with LiveComponents]]></title>
            <link>https://symfony.com/blog/symfonycon-brussels-2023-a-memorable-game-ux-with-livecomponents?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed</link>
            <description>
SymfonyCon Brussels 2023 is just around the corner and will start on:


December 5-6: Workshop days. It is possible to attend 1 two-day training or 2 one-day trainings!
December 7-8: Conference days with 3 parallels tracks and 1 unconference track…</description>
            <content:encoded><![CDATA[
                                <p><a class="block text-center" href="https://live.symfony.com/2023-brussels-con" title="Sfconbrussels2023 Header">
    <img src="https://symfony.com/uploads/assets/blog/sfconbrussels2023-header.jpg" alt="Sfconbrussels2023 Header">
</a></p>

<p><strong>SymfonyCon Brussels 2023</strong> is just around the corner and will start on:</p>

<ul>
<li><strong>December 5-6:</strong> Workshop days. It is possible to attend 1 two-day training or 2 one-day trainings!</li>
<li><strong>December 7-8:</strong> Conference days with 3 parallels tracks and 1 unconference track in English.</li>
</ul>

<h2>🎤 New speaker announcement!</h2>

<p>We are thrilled to announce you the next speaker: <strong><a href="https://connect.symfony.com/profile/simonandre">Simon André</a></strong>, Future developer, SensioLabs will present the topic <strong><a href="https://live.symfony.com/2023-brussels-con/schedule/a-memorable-game-ux-with-livecomponents">"A Memorable Game UX with LiveComponents"</a></strong>:</p>

<p>"Symfony UX empowers developers to create rich, dynamic web interfaces that bridge the gap between backend logic and a memorable user experience.</p>
                <hr style="margin-bottom: 5px" />
                <div style="font-size: 90%">
                    <a href="https://symfony.com/sponsor">Sponsor</a> the Symfony project.
                </div>
            ]]></content:encoded>
            <guid isPermaLink="false">https://symfony.com/blog/symfonycon-brussels-2023-a-memorable-game-ux-with-livecomponents?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed</guid>
            <dc:creator><![CDATA[ Eloïse Charrier ]]></dc:creator>
            <pubDate>Sat, 18 Nov 2023 15:30:00 +0100</pubDate>
            <comments>https://symfony.com/blog/symfonycon-brussels-2023-a-memorable-game-ux-with-livecomponents?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed#comments-list</comments>
        </item>
                        <item>
            <title><![CDATA[New in Symfony 6.4: Mailer, Translation, Notifier, Webhook and RemoteEvent Integrations]]></title>
            <link>https://symfony.com/blog/new-in-symfony-6-4-mailer-translation-notifier-webhook-and-remoteevent-integrations?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed</link>
            <description>Symfony provides out-of-the-box compatibility with tens of third-party services
for Mailer, Translation, Notifier, Webhook and RemoteEvent
components. In Symfony 6.4 we&#039;re introducing even more integrations:

Mailer Integrations

    Scaleway transaction…</description>
            <content:encoded><![CDATA[
                                <p>Symfony provides out-of-the-box compatibility with tens of third-party services
for <a href="https://symfony.com/mailer" class="reference external">Mailer</a>, <a href="https://symfony.com/translation" class="reference external">Translation</a>, <a href="https://symfony.com/notifier" class="reference external">Notifier</a>, <a href="https://symfony.com/web-hook" class="reference external">Webhook</a> and <a href="https://symfony.com/remote-event" class="reference external">RemoteEvent</a>
components. In Symfony 6.4 we're introducing even more integrations:</p>
<div class="section">
<h2 id="mailer-integrations"><a class="headerlink" href="#mailer-integrations" title="Permalink to this headline">Mailer Integrations</a></h2>
<ul>
    <li><a href="https://www.scaleway.com/en/transactional-email-tem/" class="reference external" rel="external noopener noreferrer" target="_blank">Scaleway</a> transaction
email integration added by <a href="https://github.com/MrMicky-FR" class="reference external" rel="external noopener noreferrer" target="_blank">MrMicky</a> in
<a href="https://github.com/symfony/symfony/pull/51014" class="reference external" rel="external noopener noreferrer" target="_blank">PR #51014</a>;</li>
<li><a href="https://developers.brevo.com/" class="reference external" rel="external noopener noreferrer" target="_blank">Brevo</a> integration added by
<a href="https://github.com/PEtanguy" class="reference external" rel="external noopener noreferrer" target="_blank">Pierre Tanguy</a> in
<a href="https://github.com/symfony/symfony/pull/50302" class="reference external" rel="external noopener noreferrer" target="_blank">PR #50302</a>.</li>
</ul>
</div>]]]></content:encoded>
            <guid isPermaLink="false">https://symfony.com/blog/new-in-symfony-6-4-autowirelocator-and-autowireiterator-attributes?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed</guid>
            <dc:creator><![CDATA[ Javier Eguiluz ]]></dc:creator>
            <pubDate>Thu, 16 Nov 2023 13:10:00 +0100</pubDate>
            <comments>https://symfony.com/blog/new-in-symfony-6-4-autowirelocator-and-autowireiterator-attributes?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed#comments-list</comments>
        </item>        
  </channel>
</rss>
XML;

    private const MALFORMED_XML = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
        <title>Symfony Blog</title>
        <atom:link href="https://feeds.feedburner.com/symfony/blog" rel="self" type="application/rss+xml" />
        <link>https://symfony.com/blog/</link>
        <description>Most recent posts published on the Symfony project blog</description>
        <pubDate>Sun, 19 Nov 2023 21:44:09 +0100</pubDate>
        <lastBuildDate>Sun, 19 Nov 2023 08:58:00 +0100</lastBuildDate>
        <language>en</language>
        <item>
            <title><![CDATA[SymfonyCon Brussels 2023: A Memorable Game UX with LiveComponents]]></title>
            <link>https://symfony.com/blog/symfonycon-brussels-2023-a-memorable-game-ux-with-livecomponents?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed</link>
            <guid isPermaLink="false">https://symfony.com/blog/symfonycon-brussels-2023-a-memorable-game-ux-with-livecomponents?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed</guid>
            <dc:creator><![CDATA[ Eloïse Charrier ]]></dc:creator>
            <comments>https://symfony.com/blog/symfonycon-brussels-2023-a-memorable-game-ux-with-livecomponents?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed#comments-list</comments>
        </item>     
  </channel>
</rss>
XML;

    protected function setUp(): void
    {
        $this->logger = new InMemoryLogger();
        $this->sourceRepository = new InMemorySourceRepository();

        $this->sourceRepository->save(SourceFactory::setup()
            ->withName(SymfonyFeedProvider::getSource())
            ->withUrl('https://feeds.feedburner.com/symfony/blog')
            ->create());

        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_fetch_the_external_feed(): void
    {
        // Arrange
        $client = new MockHttpClient([
            new MockResponse(self::EXTERNAL_FEED, ['response_headers' => ['Content-Type' => 'application/rss+xml']])
        ]);

        $feedProvider = new SymfonyFeedProvider(new SimplePieFeedParser($client, $this->logger), $this->sourceRepository);

        // Act
        $feedItems = $feedProvider->fetchFeedItems();

        // Assert
        self::assertCount(2, $feedItems);

        self::assertSame("SymfonyCon Brussels 2023: A Memorable Game UX with LiveComponents", $feedItems[0]->title);
        self::assertEquals(
            "SymfonyCon Brussels 2023 is just around the corner and will start on:   December 5-6: Workshop days. It is possible to attend 1 two-day training or 2 one-day trainings! December 7-8: Conference days with 3 parallels tracks and 1 unconference track…",
            $feedItems[0]->summary
        );
        self::assertSame(
            "https://symfony.com/blog/symfonycon-brussels-2023-a-memorable-game-ux-with-livecomponents?utm_source=Symfony%20Blog%20Feed&utm_medium=feed",
            $feedItems[0]->url
        );
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-11-18 14:30'), $feedItems[0]->updated);
        self::assertSame("symfony.com", $feedItems[0]->source);

        self::assertSame("New in Symfony 6.4: Mailer, Translation, Notifier, Webhook and RemoteEvent Integrations", $feedItems[1]->title);
        self::assertEquals(
            "Symfony provides out-of-the-box compatibility with tens of third-party services for Mailer, Translation, Notifier, Webhook and RemoteEvent components. In Symfony 6.4 we're introducing even more integrations:  Mailer Integrations      Scaleway transaction…",
            $feedItems[1]->summary
        );
        self::assertSame(
            "https://symfony.com/blog/new-in-symfony-6-4-mailer-translation-notifier-webhook-and-remoteevent-integrations?utm_source=Symfony%20Blog%20Feed&utm_medium=feed",
            $feedItems[1]->url
        );
        self::assertEquals(\DateTime::createFromFormat('Y-m-d H:i', '2023-11-16 12:10'), $feedItems[1]->updated);
        self::assertSame("symfony.com", $feedItems[1]->source);
    }

    /**
     * @test
     */
    public function it_should_get_the_source_name(): void
    {
        // Assert
        self::assertSame('symfony.com', SymfonyFeedProvider::getSource());
    }

    /**
     * @test
     */
    public function it_should_log_if_entry_cant_be_parsed(): void
    {
        // Arrange
        $client = new MockHttpClient([
            new MockResponse(self::MALFORMED_XML, ['response_headers' => ['Content-Type' => 'application/rss+xml']])
        ]);

        $feedProvider = new SymfonyFeedProvider(new SimplePieFeedParser($client, $this->logger), $this->sourceRepository);

        // Act
        $feedItems = $feedProvider->fetchFeedItems();

        // Assert
        self::assertCount(1, $this->logger->recordedLogs);

        $log = $this->logger->recordedLogs[0];

        self::assertSame(LogLevel::WARNING, $log->level);
        self::assertSame('[SimplePieParser] Could not parse entry', $log->message);
        self::assertSame([
            'source' => SymfonyFeedProvider::getSource(),
            'feed_url' => 'https://feeds.feedburner.com/symfony/blog',
            'id' => 'https://symfony.com/blog/symfonycon-brussels-2023-a-memorable-game-ux-with-livecomponents?utm_source=Symfony%20Blog%20Feed&amp;utm_medium=feed',
        ], $log->context);
    }
}

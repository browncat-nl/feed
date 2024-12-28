<?php

namespace Unit\Feed\Application\Service\FeedProvider;

use App\Feed\Application\Service\FeedProvider\MartinFowlerFeedProvider;
use App\Feed\Infrastructure\FeedParser\RssParser\SimplePieFeedParser;
use DateTime;
use Dev\Common\Infrastructure\Logger\InMemoryLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class MartinFowlerFeedProviderTest extends TestCase
{
    private InMemoryLogger $logger;

    private const EXTERNAL_FEED = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <link href="https://martinfowler.com/feed.atom" rel="self"/>
  <link href="https://martinfowler.com"/>
  <id>https://martinfowler.com/feed.atom</id>
  <title>Martin Fowler</title>
  <subtitle>Master feed of news and updates from martinfowler.com</subtitle>
  <author>
    <name>Martin Fowler</name>
    <email>martin@martinfowler.com</email>
    <uri>https://martinfowler.com</uri>
  </author>
  <updated>2023-07-27T10:34:00-04:00</updated>
<entry>
    <title>Exploring Gen AI - Three versions of a median</title>
    <link href="https://martinfowler.com/articles/exploring-gen-ai.html#median---a-tale-in-three-functions"/>
    <updated>2023-07-27T10:34:00-04:00</updated>
    <id>tag:martinfowler.com,2023-07-27:Exploring-Gen-AI---Three-versions-of-a-median</id>
    <content type="html">
&lt;p&gt;&lt;b class = 'author'&gt;Birgitta B&amp;#xF6;ckeler&lt;/b&gt; continues her explorations in using
     LLMs, this time by asking GitHub Copilot to &lt;a href = 'https://martinfowler.com/articles/exploring-gen-ai.html#median---a-tale-in-three-functions'&gt;write a median
     function&lt;/a&gt;. It gave her three suggestions to choose from. The
     experience shows you still have to know what you're doing when asking LLMs
     to write code, since the LLM's programming skills are often rather flawed.&lt;/p&gt;

&lt;p&gt;&lt;a class = 'more' href = 'https://martinfowler.com/articles/exploring-gen-ai.html#median---a-tale-in-three-functions'&gt;moreâ€¦&lt;/a&gt;&lt;/p&gt;</content>
  </entry>

<entry>
    <title>Exploring Gen AI - The toolchain</title>
    <link href="https://martinfowler.com/articles/exploring-gen-ai.html#the-toolchain"/>
    <updated>2023-07-26T10:52:00-04:00</updated>
    <id>tag:martinfowler.com,2023-07-26:Exploring-Gen-AI---The-toolchain</id>
    <content type="html">
&lt;p&gt;My colleague &lt;b class = 'author'&gt;Birgitta B&amp;#xF6;ckeler&lt;/b&gt; has long been one of our senior
     technology leaders in Germany. She's now moved into a new role coordinating
     our work with Generative AI and its effect of software delivery practices.
     She's decided to publish her exploration in a series of memos. The first
     memo looks at the &lt;a href = 'https://martinfowler.com/articles/exploring-gen-ai.html#the-toolchain'&gt;current toolchain for LLMs&lt;/a&gt;, categorizing them by what
     tasks they help with, how we interact with the LLM, and where they come from.&lt;/p&gt;

&lt;p&gt;&lt;a class = 'more' href = 'https://martinfowler.com/articles/exploring-gen-ai.html#the-toolchain'&gt;moreâ€¦&lt;/a&gt;&lt;/p&gt;</content>
  </entry>

<entry>
    <title>Bliki: TeamTopologies</title>
    <link href="https://martinfowler.com/bliki/TeamTopologies.html"/>
    <updated>2023-07-25T09:25:00-04:00</updated>
    <id>https://martinfowler.com/bliki/TeamTopologies.html</id>
    <category term="bliki"/>
    <content type="html">
&lt;div class="book-sidebar no-text"&gt;&lt;span class="img-link"&gt;&lt;a href="https://www.amazon.com/gp/product/1942788819/ref=as_li_tl?ie=UTF8&amp;amp;camp=1789&amp;amp;creative=9325&amp;amp;creativeASIN=1942788819&amp;amp;linkCode=as2&amp;amp;tag=martinfowlerc-20"&gt;&lt;img class="cover" src="https://martinfowler.com/bliki/images/team-topologies/book.jpg"&gt;&lt;/a&gt;&lt;/span&gt;&lt;/div&gt;

&lt;p&gt;Any large software effort, such as the software estate for a large
    company, requires a lot of people - and whenever you have a lot of people
    you have to figure out how to divide them into effective teams. Forming
    &lt;a href="/bliki/BusinessCapabilityCentric.html"&gt;Business Capability Centric&lt;/a&gt; teams helps software efforts to
    be responsive to customersâ€™ needs, but the range of skills required often
    overwhelms such teams. &lt;a href="https://www.amazon.com/gp/product/1942788819/ref=as_li_tl?ie=UTF8&amp;amp;camp=1789&amp;amp;creative=9325&amp;amp;creativeASIN=1942788819&amp;amp;linkCode=as2&amp;amp;tag=martinfowlerc-20"&gt;Team Topologies&lt;/a&gt; is a model
    for describing the organization of software development teams,
    developed by Matthew Skelton and Manuel Pais. It defines four forms
    of teams and three modes of team
    interactions. The model encourages healthy interactions that allow 
    business-capability centric teams to flourish in their task of providing a
    steady flow of valuable software.&lt;/p&gt;

&lt;p&gt;The primary kind of team in this framework is the &lt;b&gt;stream-aligned
    team&lt;/b&gt;, a &lt;a href="/bliki/BusinessCapabilityCentric.html"&gt;Business Capability Centric&lt;/a&gt; team that is
    responsible for software for a single business capability. These are
    long-running teams, thinking of their efforts as providing a &lt;a href="/articles/products-over-projects.html"&gt;software
    product&lt;/a&gt; to enhance the business capability.&lt;/p&gt;
</content>
  </entry>
</feed>
XML;

    private const MALFORMED_EXTERNAL_FEED = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <link href="https://martinfowler.com/feed.atom" rel="self"/>
  <link href="https://martinfowler.com"/>
  <id>https://martinfowler.com/feed.atom</id>
  <title>Martin Fowler</title>
  <subtitle>Master feed of news and updates from martinfowler.com</subtitle>
  <author>
    <name>Martin Fowler</name>
    <email>martin@martinfowler.com</email>
    <uri>https://martinfowler.com</uri>
  </author>
  <updated>2023-07-27T10:34:00-04:00</updated>
<entry>
    <title>Exploring Gen AI - Three versions of a median</title>
    <link href="https://martinfowler.com/articles/exploring-gen-ai.html#median---a-tale-in-three-functions"/>
    <id>tag:martinfowler.com,2023-07-27:Exploring-Gen-AI---Three-versions-of-a-median</id>
    <content type="html">
&lt;p&gt;&lt;b class = 'author'&gt;Birgitta B&amp;#xF6;ckeler&lt;/b&gt; continues her explorations in using
     LLMs, this time by asking GitHub Copilot to &lt;a href = 'https://martinfowler.com/articles/exploring-gen-ai.html#median---a-tale-in-three-functions'&gt;write a median
     function&lt;/a&gt;. It gave her three suggestions to choose from. The
     experience shows you still have to know what you're doing when asking LLMs
     to write code, since the LLM's programming skills are often rather flawed.&lt;/p&gt;

&lt;p&gt;&lt;a class = 'more' href = 'https://martinfowler.com/articles/exploring-gen-ai.html#median---a-tale-in-three-functions'&gt;moreâ€¦&lt;/a&gt;&lt;/p&gt;</content>
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
            new MockResponse(self::EXTERNAL_FEED, ['response_headers' => ['Content-Type' => 'application/rss+xml']])
        ]);

        $feedProvider = new MartinFowlerFeedProvider(new SimplePieFeedParser($client, $this->logger));

        // Act
        $feedItems = $feedProvider->fetchFeedItems();

        // Assert
        self::assertCount(3, $feedItems);

        self::assertSame("Exploring Gen AI - Three versions of a median", $feedItems[0]->title);
        self::assertSame(
            "Birgitta Böckeler continues her explorations in using LLMs, this time by asking GitHub Copilot to write a median function. It gave her three suggestions to choose from. The experience shows you still have to know what you're doing when asking LLMs to write code, since the LLM's programming skills are often rather flawed.",
            $feedItems[0]->summary
        );
        self::assertSame("https://martinfowler.com/articles/exploring-gen-ai.html#median---a-tale-in-three-functions", $feedItems[0]->url);
        self::assertEquals(new DateTime("2023-07-27 14:34:00.0 +00:00"), $feedItems[0]->updated);
        self::assertSame("martinfowler.com", $feedItems[0]->source);

        self::assertSame("Exploring Gen AI - The toolchain", $feedItems[1]->title);
        self::assertSame(
            "My colleague Birgitta Böckeler has long been one of our senior technology leaders in Germany. She's now moved into a new role coordinating our work with Generative AI and its effect of software delivery practices. She's decided to publish her exploration in a series of memos. The first memo looks at the current toolchain for LLMs, categorizing them by what tasks they help with, how we interact with the LLM, and where they come from.",
            $feedItems[1]->summary
        );
        self::assertSame("https://martinfowler.com/articles/exploring-gen-ai.html#the-toolchain", $feedItems[1]->url);
        self::assertEquals(new DateTime("2023-07-26T14:52:00.0 +00:00"), $feedItems[1]->updated);
        self::assertSame("martinfowler.com", $feedItems[1]->source);

        self::assertSame("Bliki: TeamTopologies", $feedItems[2]->title);
        self::assertSame(
            "Any large software effort, such as the software estate for a large company, requires a lot of people - and whenever you have a lot of people you have to figure out how to divide them into effective teams. Forming Business Capability Centric teams helps software efforts to be responsive to customersâ€™ needs, but the range of skills required often overwhelms such teams. Team Topologies is a model for describing the organization of software development teams, developed by Matthew Skelton and Manuel Pais. It defines four forms of teams and three modes of team interactions. The model encourages healthy interactions that allow business-capability centric teams to flourish in their task of providing a steady flow of valuable software.",
            $feedItems[2]->summary
        );
        self::assertSame("https://martinfowler.com/bliki/TeamTopologies.html", $feedItems[2]->url);
        self::assertEquals(new DateTime("2023-07-25T13:25:00.0 +00:00"), $feedItems[2]->updated);
        self::assertSame("martinfowler.com", $feedItems[2]->source);
    }

    /**
     * @test
     */
    public function it_should_get_the_source_name(): void
    {
        // Assert
        self::assertSame('martinfowler.com', MartinFowlerFeedProvider::getSource());
    }

    /**
     * @test
     */
    public function it_should_log_if_entry_cant_be_parsed(): void
    {
        // Arrange
        $client = new MockHttpClient([
            new MockResponse(self::MALFORMED_EXTERNAL_FEED, ['response_headers' => ['Content-Type' => 'application/rss+xml']])
        ]);

        $feedProvider = new MartinFowlerFeedProvider(new SimplePieFeedParser($client, $this->logger));

        // Act
        $feedItems = $feedProvider->fetchFeedItems();

        // Assert
        self::assertCount(0, $feedItems);

        self::assertCount(1, $this->logger->recordedLogs);

        $log = $this->logger->recordedLogs[0];

        self::assertSame(LogLevel::WARNING, $log->level);
        self::assertSame('[SimplePieParser] Could not parse entry', $log->message);
        self::assertSame([
            'source' => MartinFowlerFeedProvider::getSource(),
            'feed_url' => 'https://martinfowler.com/feed.atom',
            'id' => 'tag:martinfowler.com,2023-07-27:Exploring-Gen-AI---Three-versions-of-a-median',
        ], $log->context);
    }
}

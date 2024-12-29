<?php

namespace Feed\Application\Service\FeedItemNormalizer;

use App\Feed\Application\Service\FeedItemNormalizer\RemoveHtmlTagsNormalizer;
use App\Feed\Application\Service\FeedProvider\FeedItem;
use PHPUnit\Framework\TestCase;

class RemoveHtmlTagsNormalizerTest extends TestCase
{
    private RemoveHtmlTagsNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new RemoveHtmlTagsNormalizer();

        parent::setUp();
    }

    /**
     * @test
     */
    public function it_should_do_nothing_if_the_feed_item_does_not_contain_html(): void
    {
        // Arrange
        $feedItem = new FeedItem(
            'some title',
            'some summary',
            "https://example.com/feed",
            new \DateTime('now'),
            'some source',
        );

        // Act
        $normalizedFeedItem = $this->normalizer->__invoke($feedItem);

        // Assert
        self::assertEquals($feedItem, $normalizedFeedItem);
    }

    /**
     * @test
     */
    public function it_should_remove_html_tags_from_the_title_and_summary(): void
    {
        // Arrange
        $feedItem = new FeedItem(
            '<h1>A title with html</h1>',
            '<p>Some summary</p><li><ul>-containing html</ul></li>',
            "https://example.com/feed",
            new \DateTime('now'),
            'some source',
        );

        // Act
        $normalizedFeedItem = $this->normalizer->__invoke($feedItem);

        // Assert
        self::assertSame("A title with html", $normalizedFeedItem->title);
        self::assertSame("Some summary-containing html", $normalizedFeedItem->summary);
    }
}

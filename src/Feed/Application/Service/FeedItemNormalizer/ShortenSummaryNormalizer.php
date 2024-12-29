<?php

namespace App\Feed\Application\Service\FeedItemNormalizer;

use App\Feed\Application\FeedParser\FeedItem;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(priority: -10)]
final class ShortenSummaryNormalizer implements FeedItemNormalizer
{
    private const SUMMARY_LENGTH = 280;

    public function __invoke(FeedItem $feedItem): FeedItem
    {
        if (strlen($feedItem->summary) <= self::SUMMARY_LENGTH) {
            return $feedItem;
        }
        return new FeedItem(
            $feedItem->title,
            mb_strimwidth($feedItem->summary, 0, self::SUMMARY_LENGTH, "..."),
            $feedItem->url,
            $feedItem->updated,
            $feedItem->source,
        );
    }
}

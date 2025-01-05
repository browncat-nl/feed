<?php

namespace App\Feed\Application\Service\FeedItemNormalizer;

use App\Feed\Application\Service\FeedFetcher\FeedItem;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(priority: 20)]
final class RemoveHtmlTagsNormalizer implements FeedItemNormalizer
{
    public function __invoke(FeedItem $feedItem): FeedItem
    {
        return new FeedItem(
            trim(html_entity_decode(strip_tags($feedItem->title))),
            trim(html_entity_decode(strip_tags($feedItem->summary))),
            $feedItem->url,
            $feedItem->updated,
            $feedItem->source,
        );
    }
}

<?php

namespace App\Feed\Application\Service\FeedItemNormalizer;

use App\Feed\Application\Service\FeedFetcher\FeedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface FeedItemNormalizer
{
    public function __invoke(FeedItem $feedItem): FeedItem;
}

<?php

namespace App\Feed\Application\Command\Feed;

use App\Feed\Application\Command\Feed\Handler\NormalizeFeedItemHandler;
use App\Feed\Application\Service\FeedFetcher\FeedItem;

/**
 * @see NormalizeFeedItemHandler
 */
final readonly class NormalizeFeedItemCommand
{
    public function __construct(public FeedItem $feedItem)
    {
    }
}

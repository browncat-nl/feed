<?php

namespace App\Feed\Application\Service\FeedProvider;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface FeedProvider
{
    public static function getSource(): string;

    /**
     * @return list<FeedItem>
     */
    public function fetchFeedItems(): array;
}
